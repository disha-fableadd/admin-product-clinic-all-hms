<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\Purchases;
use App\Models\Supplier;
use App\Models\Medicine;
use App\Models\Category;
use App\Models\Brand;
use App\Models\TaxRate;
use App\Models\BankMaster;
use App\Models\Setting;
use App\Models\PaymentStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    /**
     * Display Purchases List with KPI Summary & Filters
     */
    public function purchase_list(Request $request)
    {
        $branchId = session('selectedBranchId') ?? optional(Auth::user())->branch_id ?? 1;

        $query = PurchaseInvoice::with(['vendor', 'purchases.medicine', 'payments'])
            ->where('isDeleted', 0);

        if ($branchId) {
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }

        $purchases = $query->orderBy('id', 'desc')->get();

        $totalPurchases = $purchases->sum('grand_total');
        $totalPaid = $purchases->sum('paid');
        $totalPending = $purchases->sum('remaining_amount');

        $vendors = Supplier::orderBy('name', 'asc')->get();
        $years = PurchaseInvoice::where('isDeleted', 0)
            ->selectRaw('YEAR(created_at) as yr')
            ->distinct()
            ->pluck('yr')
            ->toArray();

        if (empty($years)) {
            $years = [date('Y')];
        }

        $banks = BankMaster::where('status', 1)->where('isDeleted', 0)->get();

        return view('purchase.purchaselist', compact(
            'purchases',
            'totalPurchases',
            'totalPaid',
            'totalPending',
            'vendors',
            'years',
            'banks'
        ));
    }

    /**
     * Show Add Purchase Form
     */
    public function add_purchase(Request $request)
    {
        $branchId = session('selectedBranchId') ?? optional(Auth::user())->branch_id ?? 1;

        $vendors = Supplier::orderBy('name', 'asc')->get();
        $categories = Category::where('branch_id', $branchId)->orWhereNull('branch_id')->get();
        $brands = Brand::where('branch_id', $branchId)->orWhereNull('branch_id')->get();
        $taxes = TaxRate::where('status', 'active')->where('isDeleted', 0)->get();
        $medicines = Medicine::orderBy('name', 'asc')->get();
        $banks = BankMaster::where('status', 1)->where('isDeleted', 0)->get();

        return view('purchase.addpurchase', compact(
            'vendors',
            'categories',
            'brands',
            'taxes',
            'medicines',
            'banks'
        ));
    }

    /**
     * Show Edit Purchase Form
     */
    public function edit_purchase($id)
    {
        $invoice = PurchaseInvoice::with(['vendor', 'purchases.medicine', 'payments'])->findOrFail($id);
        $branchId = session('selectedBranchId') ?? optional(Auth::user())->branch_id ?? 1;

        $vendors = Supplier::orderBy('name', 'asc')->get();
        $categories = Category::where('branch_id', $branchId)->orWhereNull('branch_id')->get();
        $brands = Brand::where('branch_id', $branchId)->orWhereNull('branch_id')->get();
        $taxes = TaxRate::where('status', 'active')->where('isDeleted', 0)->get();
        $medicines = Medicine::orderBy('name', 'asc')->get();
        $banks = BankMaster::where('status', 1)->where('isDeleted', 0)->get();

        return view('purchase.editpurchase', compact(
            'invoice',
            'vendors',
            'categories',
            'brands',
            'taxes',
            'medicines',
            'banks'
        ));
    }

    /**
     * Store New Purchase (AJAX & Form)
     */
    public function store(Request $request)
    {
        // Resolve items from items array, products array, or form field arrays
        $items = $request->items ?? $request->products ?? [];
        if (empty($items) && $request->has('product_name') && is_array($request->product_name)) {
            $pNames = $request->product_name;
            $cats = $request->category_name ?? [];
            $batches = $request->batch_no ?? [];
            $types = $request->purchase_type ?? [];
            $qtys = $request->quantity ?? [];
            $rates = $request->price ?? [];
            $dPercents = $request->discount_percent ?? [];
            $dPrices = $request->discount_price ?? [];
            $brands = $request->brand_name ?? [];
            $expDates = $request->exp_date ?? [];
            $totals = $request->total ?? [];

            foreach ($pNames as $idx => $pVal) {
                if (empty($pVal)) continue;
                $items[] = [
                    'product_id' => is_numeric($pVal) ? (int)$pVal : null,
                    'product_name' => is_numeric($pVal) ? optional(Medicine::find($pVal))->name : $pVal,
                    'category_id' => $cats[$idx] ?? null,
                    'batch_no' => $batches[$idx] ?? null,
                    'purchase_type' => $types[$idx] ?? 'Local',
                    'quantity' => (float)($qtys[$idx] ?? 1),
                    'purchase_rate' => (float)($rates[$idx] ?? 0),
                    'price' => (float)($rates[$idx] ?? 0),
                    'discount_percent' => (float)($dPercents[$idx] ?? 0),
                    'discount_amount' => (float)($dPrices[$idx] ?? 0),
                    'brand' => $brands[$idx] ?? null,
                    'expiry_date' => $expDates[$idx] ?? null,
                    'exp_date' => $expDates[$idx] ?? null,
                    'total' => (float)($totals[$idx] ?? 0),
                ];
            }
        }
        $request->merge(['items' => $items]);

        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'items' => 'required|array|min:1',
            'grand_total' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $branchId = session('selectedBranchId') ?? optional(Auth::user())->branch_id ?? 1;
            $userId = optional(Auth::user())->id ?? 1;

            // 1. Resolve or Create Vendor / Supplier
            $vendorId = null;
            if (is_numeric($request->vendor_id)) {
                $vendor = Supplier::find($request->vendor_id);
                if ($vendor) {
                    $vendorId = $vendor->id;
                    if ($request->filled('vendor_phone')) {
                        $vendor->phone = $request->vendor_phone;
                        $vendor->save();
                    }
                }
            }
            if (!$vendorId) {
                $vendor = Supplier::create([
                    'user_id' => $userId ?: 1,
                    'name' => $request->vendor_id,
                    'contact_person' => $request->vendor_id ?? 'Supplier',
                    'phone' => $request->vendor_phone ?? '0000000000',
                    'address' => $request->vendor_address ?? 'Supplier Address',
                    'branch_id' => $branchId,
                ]);
                $vendorId = $vendor->id;
            }

            // 2. Bill Number / Invoice Number
            $billNo = $request->bill_no;
            $latestId = (PurchaseInvoice::max('id') ?? 0) + 1;
            $invoiceNumber = $billNo ?: ('PINV-' . str_pad($latestId, 6, '0', STR_PAD_LEFT));

            $grandTotal = (float) $request->grand_total;
            $paymentMode = strtolower($request->payment_mode ?? 'cash');
            $paidType = strtolower($request->paid_type ?? 'full');

            $cashAmount = (float) ($request->cash_amount ?? 0);
            $onlineAmount = (float) ($request->online_amount ?? 0);
            $enterAmount = (float) ($request->enter_amount ?? 0);

            if ($paymentMode === 'pay_later') {
                $paid = 0.00;
            } elseif ($paymentMode === 'cash_online') {
                if ($paidType === 'full') {
                    $paid = $grandTotal;
                    $cashAmount = (float)($request->cash_amount ?: ($grandTotal / 2));
                    $onlineAmount = $grandTotal - $cashAmount;
                } else {
                    $paid = min($grandTotal, $cashAmount + $onlineAmount);
                }
            } else {
                if ($paidType === 'full') {
                    $paid = $grandTotal;
                } else {
                    $paid = min($grandTotal, $enterAmount > 0 ? $enterAmount : (float)($request->paid ?? 0));
                }
                if ($paymentMode === 'cash') {
                    $cashAmount = $paid;
                    $onlineAmount = 0.00;
                } else {
                    $onlineAmount = $paid;
                    $cashAmount = 0.00;
                }
            }

            $remaining = max(0, $grandTotal - $paid);
            $status = $remaining <= 0 ? 'completed' : ($paid > 0 ? 'partially' : 'pending');

            // 3. Create Purchase Invoice
            $purchaseInvoice = PurchaseInvoice::create([
                'invoice_number' => $invoiceNumber,
                'vendor_id' => $vendorId,
                'bill_no' => $billNo,
                'products' => $request->items,
                'total_amount' => $request->subtotal ?? $grandTotal,
                'paid' => $paid,
                'discount' => $request->discount ?? 0,
                'shipping' => $request->shipping ?? 0,
                'taxes' => is_array($request->taxes) ? json_encode($request->taxes) : ($request->taxes ?? null),
                'grand_total' => $grandTotal,
                'remaining_amount' => $remaining,
                'gst_option' => $request->gst_option ?? 'without_gst',
                'status' => $status,
                'branch_id' => $branchId,
                'created_by' => $userId,
                'payment_due_days' => $request->payment_due_days ?? null,
                'due_date' => $request->due_date ?? null,
                'interest_type' => $request->interest_before_due ?? null,
                'interest_amount' => $request->interest_after_due ?? null,
                'final_amount_after_interest' => $grandTotal,
                'isDeleted' => 0,
            ]);

            // 4. Save Purchases Items & Increment Inventory Stock
            foreach ($request->items as $item) {
                $qty = (int) ($item['quantity'] ?? 1);
                $rate = (float) ($item['price'] ?? ($item['purchase_rate'] ?? 0));
                $mrp = (float) ($item['mrp'] ?? $rate);
                $discPercent = (float) ($item['discount_percent'] ?? 0);
                $discAmount = (float) ($item['discount_amount'] ?? (($qty * $rate) * ($discPercent / 100)));
                $total = (float) ($item['total'] ?? (($qty * $rate) - $discAmount));

                $productId = !empty($item['product_id']) && is_numeric($item['product_id']) ? (int) $item['product_id'] : null;
                $productName = $item['product_name'] ?? ($item['name'] ?? 'Medicine');

                // Update / Find Medicine Stock
                $med = null;
                if ($productId) {
                    $med = Medicine::find($productId);
                } elseif (!empty($productName)) {
                    $med = Medicine::where('name', $productName)->first();
                }

                if (!$med) {
                    $med = Medicine::create([
                        'user_id' => $userId ?: 1,
                        'name' => $productName,
                        'service_type' => 'Medicine',
                        'category_id' => !empty($item['category_id']) ? $item['category_id'] : 1,
                        'brand_id' => !empty($item['brand_id']) ? $item['brand_id'] : null,
                        'quantity' => $qty,
                        'purchase_price' => $rate,
                        'mrp' => $mrp > 0 ? $mrp : $rate,
                        'unit' => (string) ($mrp > 0 ? $mrp : ($rate > 0 ? $rate : '100.00')),
                        'medicine_unit' => $item['medicine_unit'] ?? ($item['pack'] ?? 'Box'),
                        'stock_value' => $qty * $rate,
                        'availablility' => 'in_stock',
                        'batch_no' => !empty($item['batch_no']) ? $item['batch_no'] : ('BAT' . date('ymd') . rand(10, 99)),
                        'expiry_date' => !empty($item['expiry_date']) ? date('Y-m-d', strtotime($item['expiry_date'])) : now()->addYears(2)->toDateString(),
                        'manufacture_date' => !empty($item['mfg_date']) ? date('Y-m-d', strtotime($item['mfg_date'])) : now()->toDateString(),
                        'gst_option' => $request->gst_option ?? 'Without GST',
                        'branch_id' => $branchId,
                        'status' => 'active',
                    ]);
                } else {
                    $med->quantity = ((int)$med->quantity) + $qty;
                    $med->availablility = 'in_stock';
                    if ($rate > 0) $med->purchase_price = $rate;
                    if ($mrp > 0) {
                        $med->mrp = $mrp;
                        $med->unit = $mrp;
                    }
                    if (!empty($item['batch_no'])) $med->batch_no = $item['batch_no'];
                    if (!empty($item['expiry_date'])) $med->expiry_date = date('Y-m-d', strtotime($item['expiry_date']));
                    if (!empty($item['category_id'])) $med->category_id = $item['category_id'];
                    if (!empty($item['brand_id'])) $med->brand_id = $item['brand_id'];
                    $med->stock_value = $med->quantity * ((float)($med->purchase_price ?: $med->mrp ?: 0));
                    $med->save();
                }

                Purchases::create([
                    'item' => (string) $med->id,
                    'quantity' => $qty,
                    'batch_no' => $item['batch_no'] ?? null,
                    'brand_id' => $item['brand_id'] ?? null,
                    'mrp' => $mrp,
                    'exp_date' => !empty($item['expiry_date']) ? date('Y-m-d', strtotime($item['expiry_date'])) : null,
                    'free_quantity' => $item['free_quantity'] ?? 0,
                    'price' => $rate,
                    'discount_percent' => $discPercent,
                    'discount_amount' => $discAmount,
                    'purchase_date' => $request->purchase_date ?? now()->toDateString(),
                    'purchase_status' => 'completed',
                    'payment_status' => $remaining <= 0 ? 'paid' : ($paid > 0 ? 'partially' : 'pending'),
                    'amount_total' => $total,
                    'purchase_type' => $item['purchase_type'] ?? 'Local',
                    'vendor_id' => $vendorId,
                    'invoice_id' => (string) $purchaseInvoice->id,
                    'branch_id' => $branchId,
                    'created_by' => $userId,
                    'isDeleted' => 0,
                ]);
            }

            // 5. Save Payment in PaymentStore if paid > 0
            if ($paid > 0) {
                PaymentStore::create([
                    'user_id' => $vendorId,
                    'invoice_id' => $purchaseInvoice->id,
                    'payment_amount' => $paid,
                    'payment_date' => now()->toDateString(),
                    'payment_type' => 'purchase',
                    'payment_method' => $paymentMode,
                    'cash_amount' => $cashAmount,
                    'upi_amount' => $onlineAmount,
                    'remaining_amount' => (string) $remaining,
                    'remarks' => $request->remarks ?? 'Payment on purchase bill generation',
                    'isDeleted' => 0,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Purchase bill created successfully!',
                'redirect' => route('purchase.lists'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error saving purchase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Existing Purchase Bill
     */
    public function update(Request $request, $id)
    {
        $invoice = PurchaseInvoice::with('purchases')->findOrFail($id);

        // Resolve items from items array, products array, or form field arrays
        $items = $request->items ?? $request->products ?? [];
        if (empty($items) && $request->has('product_name') && is_array($request->product_name)) {
            $pNames = $request->product_name;
            $cats = $request->category_name ?? [];
            $batches = $request->batch_no ?? [];
            $types = $request->purchase_type ?? [];
            $qtys = $request->quantity ?? [];
            $rates = $request->price ?? [];
            $dPercents = $request->discount_percent ?? [];
            $dPrices = $request->discount_price ?? [];
            $brands = $request->brand_name ?? [];
            $expDates = $request->exp_date ?? [];
            $totals = $request->total ?? [];

            foreach ($pNames as $idx => $pVal) {
                if (empty($pVal)) continue;
                $items[] = [
                    'product_id' => is_numeric($pVal) ? (int)$pVal : null,
                    'product_name' => is_numeric($pVal) ? optional(Medicine::find($pVal))->name : $pVal,
                    'category_id' => $cats[$idx] ?? null,
                    'batch_no' => $batches[$idx] ?? null,
                    'purchase_type' => $types[$idx] ?? 'Local',
                    'quantity' => (float)($qtys[$idx] ?? 1),
                    'purchase_rate' => (float)($rates[$idx] ?? 0),
                    'price' => (float)($rates[$idx] ?? 0),
                    'discount_percent' => (float)($dPercents[$idx] ?? 0),
                    'discount_amount' => (float)($dPrices[$idx] ?? 0),
                    'brand' => $brands[$idx] ?? null,
                    'expiry_date' => $expDates[$idx] ?? null,
                    'exp_date' => $expDates[$idx] ?? null,
                    'total' => (float)($totals[$idx] ?? 0),
                ];
            }
        }
        $request->merge(['items' => $items]);

        // Resolve items from items array, products array, or form field arrays
        $items = $request->items ?? $request->products ?? [];
        if (empty($items) && $request->has('product_name') && is_array($request->product_name)) {
            $pNames = $request->product_name;
            $cats = $request->category_name ?? [];
            $batches = $request->batch_no ?? [];
            $types = $request->purchase_type ?? [];
            $qtys = $request->quantity ?? [];
            $rates = $request->price ?? [];
            $dPercents = $request->discount_percent ?? [];
            $dPrices = $request->discount_price ?? [];
            $brands = $request->brand_name ?? [];
            $expDates = $request->exp_date ?? [];
            $totals = $request->total ?? [];

            foreach ($pNames as $idx => $pVal) {
                if (empty($pVal)) continue;
                $items[] = [
                    'product_id' => is_numeric($pVal) ? (int)$pVal : null,
                    'product_name' => is_numeric($pVal) ? optional(Medicine::find($pVal))->name : $pVal,
                    'category_id' => $cats[$idx] ?? null,
                    'batch_no' => $batches[$idx] ?? null,
                    'purchase_type' => $types[$idx] ?? 'Local',
                    'quantity' => (float)($qtys[$idx] ?? 1),
                    'purchase_rate' => (float)($rates[$idx] ?? 0),
                    'price' => (float)($rates[$idx] ?? 0),
                    'discount_percent' => (float)($dPercents[$idx] ?? 0),
                    'discount_amount' => (float)($dPrices[$idx] ?? 0),
                    'brand' => $brands[$idx] ?? null,
                    'expiry_date' => $expDates[$idx] ?? null,
                    'exp_date' => $expDates[$idx] ?? null,
                    'total' => (float)($totals[$idx] ?? 0),
                ];
            }
        }
        $request->merge(['items' => $items]);

        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'items' => 'required|array|min:1',
            'grand_total' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $branchId = session('selectedBranchId') ?? optional(Auth::user())->branch_id ?? $invoice->branch_id ?? 1;
            $userId = optional(Auth::user())->id ?? 1;

            // 1. Revert previous inventory stock
            foreach ($invoice->purchases as $oldPurchase) {
                if ($oldPurchase->item && is_numeric($oldPurchase->item)) {
                    $med = Medicine::find($oldPurchase->item);
                    if ($med) {
                        $revQty = max(0, ((int) $med->quantity) - ((int) $oldPurchase->quantity) - ((int) $oldPurchase->free_quantity));
                        $med->quantity = $revQty;
                        $med->availablility = $revQty > 0 ? 'in_stock' : 'out_stock';
                        $med->save();
                    }
                }
            }
            Purchases::where('invoice_id', $invoice->id)->delete();

            // 2. Resolve / Update Vendor
            $vendorId = $invoice->vendor_id;
            if (is_numeric($request->vendor_id)) {
                $vendor = Supplier::find($request->vendor_id);
                if ($vendor) {
                    $vendorId = $vendor->id;
                    if ($request->filled('vendor_phone')) {
                        $vendor->phone = $request->vendor_phone;
                        $vendor->save();
                    }
                }
            }

            $grandTotal = (float) $request->grand_total;
            $paymentMode = strtolower($request->payment_mode ?? $invoice->payment_mode ?? 'cash');
            $paidType = strtolower($request->paid_type ?? 'full');

            $cashAmount = (float) ($request->cash_amount ?? 0);
            $onlineAmount = (float) ($request->online_amount ?? 0);
            $enterAmount = (float) ($request->enter_amount ?? 0);

            if ($paymentMode === 'pay_later') {
                $paid = 0.00;
            } elseif ($paymentMode === 'cash_online') {
                if ($paidType === 'full') {
                    $paid = $grandTotal;
                    $cashAmount = (float)($request->cash_amount ?: ($grandTotal / 2));
                    $onlineAmount = $grandTotal - $cashAmount;
                } else {
                    $paid = min($grandTotal, $cashAmount + $onlineAmount);
                }
            } else {
                if ($paidType === 'full') {
                    $paid = $grandTotal;
                } else {
                    $paid = min($grandTotal, $enterAmount > 0 ? $enterAmount : (float)($request->paid ?? $invoice->paid ?? 0));
                }
                if ($paymentMode === 'cash') {
                    $cashAmount = $paid;
                    $onlineAmount = 0.00;
                } else {
                    $onlineAmount = $paid;
                    $cashAmount = 0.00;
                }
            }

            $remaining = max(0, $grandTotal - $paid);
            $status = $remaining <= 0 ? 'completed' : ($paid > 0 ? 'partially' : 'pending');

            // 3. Update Invoice
            $invoice->vendor_id = $vendorId;
            if ($request->filled('bill_no')) $invoice->bill_no = $request->bill_no;
            $invoice->products = $request->items;
            $invoice->total_amount = $request->subtotal ?? $grandTotal;
            $invoice->paid = $paid;
            $invoice->discount = $request->discount ?? 0;
            $invoice->shipping = $request->shipping ?? 0;
            $invoice->grand_total = $grandTotal;
            $invoice->remaining_amount = $remaining;
            $invoice->gst_option = $request->gst_option ?? 'without_gst';
            $invoice->status = $status;
            $invoice->payment_due_days = $request->payment_due_days ?? null;
            $invoice->due_date = $request->due_date ?? null;
            $invoice->interest_type = $request->interest_before_due ?? null;
            $invoice->interest_amount = $request->interest_after_due ?? null;
            $invoice->final_amount_after_interest = $grandTotal;
            $invoice->updated_by = $userId;
            $invoice->save();

            // 4. Create New Purchases & Update Stock
            foreach ($request->items as $item) {
                $qty = (int) ($item['quantity'] ?? 1);
                $rate = (float) ($item['price'] ?? ($item['purchase_rate'] ?? 0));
                $mrp = (float) ($item['mrp'] ?? $rate);
                $discPercent = (float) ($item['discount_percent'] ?? 0);
                $discAmount = (float) ($item['discount_amount'] ?? (($qty * $rate) * ($discPercent / 100)));
                $total = (float) ($item['total'] ?? (($qty * $rate) - $discAmount));

                $productId = !empty($item['product_id']) && is_numeric($item['product_id']) ? (int) $item['product_id'] : null;
                $productName = $item['product_name'] ?? ($item['name'] ?? 'Medicine');

                $med = null;
                if ($productId) {
                    $med = Medicine::find($productId);
                } elseif (!empty($productName)) {
                    $med = Medicine::where('name', $productName)->first();
                }

                if (!$med) {
                    $med = Medicine::create([
                        'user_id' => $userId ?: 1,
                        'name' => $productName,
                        'service_type' => 'Medicine',
                        'category_id' => !empty($item['category_id']) ? $item['category_id'] : 1,
                        'brand_id' => !empty($item['brand_id']) ? $item['brand_id'] : null,
                        'quantity' => $qty,
                        'purchase_price' => $rate,
                        'mrp' => $mrp > 0 ? $mrp : $rate,
                        'unit' => (string) ($mrp > 0 ? $mrp : ($rate > 0 ? $rate : '100.00')),
                        'medicine_unit' => $item['medicine_unit'] ?? ($item['pack'] ?? 'Box'),
                        'stock_value' => $qty * $rate,
                        'availablility' => 'in_stock',
                        'batch_no' => !empty($item['batch_no']) ? $item['batch_no'] : ('BAT' . date('ymd') . rand(10, 99)),
                        'expiry_date' => !empty($item['expiry_date']) ? date('Y-m-d', strtotime($item['expiry_date'])) : now()->addYears(2)->toDateString(),
                        'manufacture_date' => !empty($item['mfg_date']) ? date('Y-m-d', strtotime($item['mfg_date'])) : now()->toDateString(),
                        'gst_option' => $request->gst_option ?? 'Without GST',
                        'branch_id' => $branchId,
                        'status' => 'active',
                    ]);
                } else {
                    $med->quantity = ((int)$med->quantity) + $qty;
                    $med->availablility = 'in_stock';
                    if ($rate > 0) $med->purchase_price = $rate;
                    if ($mrp > 0) {
                        $med->mrp = $mrp;
                        $med->unit = $mrp;
                    }
                    if (!empty($item['batch_no'])) $med->batch_no = $item['batch_no'];
                    if (!empty($item['expiry_date'])) $med->expiry_date = date('Y-m-d', strtotime($item['expiry_date']));
                    if (!empty($item['category_id'])) $med->category_id = $item['category_id'];
                    if (!empty($item['brand_id'])) $med->brand_id = $item['brand_id'];
                    $med->stock_value = $med->quantity * ((float)($med->purchase_price ?: $med->mrp ?: 0));
                    $med->save();
                }

                Purchases::create([
                    'item' => (string) $med->id,
                    'quantity' => $qty,
                    'batch_no' => $item['batch_no'] ?? null,
                    'brand_id' => $item['brand_id'] ?? null,
                    'mrp' => $mrp,
                    'exp_date' => !empty($item['expiry_date']) ? date('Y-m-d', strtotime($item['expiry_date'])) : null,
                    'free_quantity' => $item['free_quantity'] ?? 0,
                    'price' => $rate,
                    'discount_percent' => $discPercent,
                    'discount_amount' => $discAmount,
                    'purchase_date' => $request->purchase_date ?? now()->toDateString(),
                    'purchase_status' => 'completed',
                    'payment_status' => $remaining <= 0 ? 'paid' : ($paid > 0 ? 'partially' : 'pending'),
                    'amount_total' => $total,
                    'purchase_type' => $item['purchase_type'] ?? 'Local',
                    'vendor_id' => $vendorId,
                    'invoice_id' => (string) $invoice->id,
                    'branch_id' => $branchId,
                    'created_by' => $userId,
                    'isDeleted' => 0,
                ]);
            }

            // 5. Update / Record Payment
            if ($paid > 0) {
                PaymentStore::create([
                    'user_id' => $vendorId,
                    'invoice_id' => $invoice->id,
                    'payment_amount' => $paid,
                    'payment_date' => now()->toDateString(),
                    'payment_type' => 'purchase',
                    'payment_method' => $paymentMode,
                    'cash_amount' => $cashAmount,
                    'upi_amount' => $onlineAmount,
                    'remaining_amount' => (string) $remaining,
                    'remarks' => $request->remarks ?? 'Payment updated via Edit Purchase',
                    'isDeleted' => 0,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Purchase bill updated successfully!',
                'redirect' => route('purchase.lists'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error updating purchase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit Make Payment for Purchase
     */
    public function makePaymentSubmit(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
        ]);

        $invoice = PurchaseInvoice::findOrFail($request->invoice_id);

        $payAmount = (float) $request->amount;
        $currentPaid = (float) $invoice->paid;
        $grandTotal = (float) ($invoice->final_amount_after_interest ?: $invoice->grand_total);

        $newPaid = min($grandTotal, $currentPaid + $payAmount);
        $newRemaining = max(0, $grandTotal - $newPaid);
        $status = $newRemaining <= 0 ? 'completed' : 'partially';

        $invoice->paid = $newPaid;
        $invoice->remaining_amount = $newRemaining;
        $invoice->status = $status;
        $invoice->save();

        PaymentStore::create([
            'user_id' => $invoice->vendor_id,
            'invoice_id' => $invoice->id,
            'payment_amount' => $payAmount,
            'payment_date' => $request->payment_date ?: now()->toDateString(),
            'payment_type' => 'purchase',
            'payment_method' => $request->payment_method,
            'cash_amount' => (float)($request->cash_amount ?? 0),
            'upi_amount' => (float)($request->online_amount ?? 0),
            'remaining_amount' => (string) $newRemaining,
            'remarks' => $request->remarks ?: 'Payment received for purchase',
            'isDeleted' => 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Payment recorded successfully!',
            'paid' => $newPaid,
            'remaining_amount' => $newRemaining,
            'purchase_status' => $status
        ]);
    }

    /**
     * Get Payment History for Purchase
     */
    public function getPaymentHistory($id)
    {
        $invoice = PurchaseInvoice::with('vendor')->findOrFail($id);

        $payments = PaymentStore::where('invoice_id', $invoice->id)
            ->where('isDeleted', 0)
            ->orderBy('id', 'desc')
            ->get();

        $totalPaid = $payments->sum('payment_amount');
        $grandTotal = (float) ($invoice->final_amount_after_interest ?: $invoice->grand_total);
        $remaining = max(0, $grandTotal - $totalPaid);

        return response()->json([
            'status' => true,
            'invoice' => $invoice,
            'payments' => $payments,
            'total_amount' => $grandTotal,
            'paid_amount' => $totalPaid,
            'remaining_amount' => $remaining,
            'return_amount' => 0.00,
        ]);
    }

    /**
     * Delete Purchase & Reverse Inventory Stock
     */
    public function deletePurchase($id)
    {
        $invoice = PurchaseInvoice::with('purchases')->findOrFail($id);

        try {
            DB::beginTransaction();

            foreach ($invoice->purchases as $p) {
                if ($p->item && is_numeric($p->item)) {
                    $med = Medicine::find($p->item);
                    if ($med) {
                        $revQty = max(0, ((int) $med->quantity) - ((int) $p->quantity) - ((int) $p->free_quantity));
                        $med->quantity = $revQty;
                        $med->availablility = $revQty > 0 ? 'in_stock' : 'out_stock';
                        $med->stock_value = $revQty * ((float) ($med->purchase_price ?: $med->mrp ?: 0));
                        $med->save();
                    }
                }
            }

            $invoice->isDeleted = 1;
            $invoice->save();
            Purchases::where('invoice_id', $invoice->id)->update(['isDeleted' => 1]);
            PaymentStore::where('invoice_id', $invoice->id)->update(['isDeleted' => 1]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Purchase bill deleted and inventory stock restored successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error deleting purchase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Purchase Details View
     */
    public function purchase_details($id)
    {
        $invoice = PurchaseInvoice::with(['vendor', 'purchases.medicine', 'payments'])->findOrFail($id);
        $setting = Setting::first();
        return view('purchase.purchase-details', compact('invoice', 'setting'));
    }

    /**
     * Purchase Invoice View
     */
    public function purchase_invoice($id)
    {
        $invoice = PurchaseInvoice::with(['vendor', 'purchases.medicine', 'payments'])->findOrFail($id);
        $setting = Setting::first();
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $branchId = $invoice->branch_id ?? session('selectedBranchId') ?? optional(Auth::user())->branch_id ?? 1;
        // $branch = \App\Models\Branch::find($branchId);
        return view('purchase.purchase_invoice', compact('invoice', 'setting', 'settings'));
    }

    /**
     * Export Purchase to Excel / CSV
     */
    public function exportPurchaseExcel()
    {
        $branchId = session('selectedBranchId') ?? optional(Auth::user())->branch_id ?? 1;

        $purchases = PurchaseInvoice::with(['vendor'])
            ->where('isDeleted', 0)
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            })
            ->orderBy('id', 'desc')
            ->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=purchases_" . date('Y-m-d') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Bill Number', 'Purchase Date', 'Vendor Name', 'Due Date', 'Grand Total', 'Paid Amount', 'Remaining', 'Status'];

        $callback = function () use ($purchases, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($purchases as $p) {
                fputcsv($file, [
                    $p->bill_no ?: $p->invoice_number,
                    $p->created_at ? $p->created_at->format('d-M-Y') : '-',
                    $p->vendor ? $p->vendor->name : 'N/A',
                    $p->due_date ? date('d-M-Y', strtotime($p->due_date)) : '-',
                    number_format($p->grand_total, 2, '.', ''),
                    number_format($p->paid, 2, '.', ''),
                    number_format($p->remaining_amount, 2, '.', ''),
                    ucfirst($p->status)
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Purchase to PDF
     */
    public function exportPurchasePdf()
    {
        $branchId = session('selectedBranchId') ?? optional(Auth::user())->branch_id ?? 1;
        $purchases = PurchaseInvoice::with(['vendor'])
            ->where('isDeleted', 0)
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            })
            ->orderBy('id', 'desc')
            ->get();

        $setting = Setting::first();
        return view('purchase.purchase-report-pdf', compact('purchases', 'setting'));
    }

    /**
     * Quick Add Vendor AJAX
     */
    public function storeVendorAjax(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:50',
            'drug_license_no' => 'nullable|string|max:100',
            'pan_no' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'account_no' => 'nullable|string|max:100',
            'ifsc_code' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric',
        ]);

        $branchId = session('selectedBranchId') ?? optional(Auth::user())->branch_id ?? 1;
        $userId = optional(Auth::user())->id ?? 1;

        $vendor = Supplier::create([
            'name' => $validated['name'],
            'contact_person' => $validated['contact_person'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? '',
            'gst_number' => $validated['gst_number'] ?? null,
            'drug_license_no' => $validated['drug_license_no'] ?? null,
            'pan_no' => $validated['pan_no'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'account_no' => $validated['account_no'] ?? null,
            'ifsc_code' => $validated['ifsc_code'] ?? null,
            'opening_balance' => $validated['opening_balance'] ?? 0.00,
            'user_id' => $userId,
            'branch_id' => $branchId,
        ]);

        return response()->json([
            'status' => true,
            'vendor' => $vendor,
            'message' => 'Vendor created successfully.'
        ]);
    }

    /**
     * Quick Add Bank AJAX
     */
    public function storeBankAjax(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'ifsc_code' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        $bank = BankMaster::create([
            'bank_name' => $validated['bank_name'],
            'name' => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'ifsc_code' => $validated['ifsc_code'] ?? '',
            'branch_name' => $validated['branch_name'] ?? '',
            'opening_balance' => $validated['opening_balance'] ?? 0,
            'status' => 1,
            'isDeleted' => 0,
        ]);

        return response()->json([
            'status' => true,
            'bank' => $bank,
            'message' => 'Bank account added successfully.'
        ]);
    }
}
