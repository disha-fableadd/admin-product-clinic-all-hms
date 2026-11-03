<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use App\Models\Purchases;
use App\Models\Supplier;
use App\Models\Medicine;
use App\Models\PaymentStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    public function purchase_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'products' => 'required|array|min:1',
            'grand_total' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $branchId = $request->branch_id ?? session('selectedBranchId') ?? optional(Auth::user())->branch_id ?? 1;
            $userId = optional(Auth::user())->id ?: (auth()->id() ?: 1);

            // 1. Resolve or Create Vendor / Supplier
            $vendorId = null;
            if (is_numeric($request->vendor_id)) {
                $vendor = Supplier::find($request->vendor_id);
                if ($vendor) {
                    $vendorId = $vendor->id;
                }
            }
            if (!$vendorId) {
                $vendor = Supplier::firstOrCreate(
                    ['name' => $request->vendor_id],
                    [
                        'user_id' => $userId ?: 1,
                        'contact_person' => $request->vendor_id ?? 'Supplier',
                        'phone' => $request->vendor_phone ?? '0000000000',
                        'email' => $request->vendor_email ?? null,
                        'gst_number' => $request->vendor_gstin ?? null,
                        'address' => $request->vendor_address ?? 'Supplier Address',
                        'branch_id' => $branchId,
                    ]
                );
                $vendorId = $vendor->id;
            }

            // 2. Generate or use Bill / Invoice Number
            $billNo = $request->bill_no;
            $invoiceNumber = $billNo ?: ('PINV-' . str_pad((PurchaseInvoice::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT));

            $grandTotal = (float) $request->grand_total;
            $paid = (float) ($request->paid ?? $grandTotal);
            $remaining = max(0, $grandTotal - $paid);
            $status = $remaining <= 0 ? 'completed' : 'pending';

            // 3. Create Purchase Invoice
            $purchaseInvoice = PurchaseInvoice::create([
                'invoice_number' => $invoiceNumber,
                'vendor_id' => $vendorId,
                'bill_no' => $billNo,
                'products' => $request->products,
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
                'due_date' => $request->due_date ?? null,
                'isDeleted' => 0,
            ]);

            // 4. Save Purchases Lines & Increment Inventory Stock
            foreach ($request->products as $p) {
                $qty = (int) ($p['quantity'] ?? 1);
                $freeQty = (int) ($p['free_quantity'] ?? 0);
                $totalQty = $qty + $freeQty;
                $price = (float) ($p['price'] ?? 0);
                $mrp = (float) ($p['mrp'] ?? $price);
                $total = (float) ($p['total'] ?? ($qty * $price));

                $productId = !empty($p['id']) && is_numeric($p['id']) ? (int) $p['id'] : null;
                $productName = !empty($p['name']) ? trim($p['name']) : (!empty($p['product_name']) ? trim($p['product_name']) : (!empty($p['item']) ? trim($p['item']) : null));

                if (!$productName && !empty($p['id']) && !is_numeric($p['id'])) {
                    $productName = trim((string) $p['id']);
                }
                if (!$productName && !empty($p['extraction_metadata']['name'])) {
                    $productName = trim((string) $p['extraction_metadata']['name']);
                }
                if (!$productName) {
                    $productName = 'Medicine Item';
                }

                // If product already exists in medicines table
                $med = null;
                if ($productId) {
                    $med = Medicine::find($productId);
                }
                if (!$med && !empty($productName)) {
                    $med = Medicine::where('name', $productName)->first();
                }

                if (!$med) {
                    $expDate = null;
                    if (!empty($p['exp_date'])) {
                        $rawExp = $p['exp_date'];
                        if (preg_match('/^\d{4}-\d{2}$/', $rawExp)) {
                            $expDate = $rawExp . '-01';
                        } else {
                            $time = strtotime($rawExp);
                            $expDate = $time ? date('Y-m-d', $time) : null;
                        }
                    }
                    if (!$expDate) {
                        $expDate = now()->addYears(2)->toDateString();
                    }

                    $mfgDate = !empty($p['mfg_date']) ? date('Y-m-d', strtotime($p['mfg_date'])) : now()->toDateString();
                    $batchNo = !empty($p['batch_no']) ? $p['batch_no'] : ('BAT' . date('ymd') . rand(10, 99));
                    $categoryId = !empty($p['category_id']) && is_numeric($p['category_id']) ? (int)$p['category_id'] : 1;
                    $unitStr = (string) ($mrp > 0 ? $mrp : ($price > 0 ? $price : '100.00'));
                    $gstOption = !empty($request->gst_option) ? $request->gst_option : 'Without GST';

                    // Create new medicine record
                    $med = Medicine::create([
                        'user_id' => $userId ?: 1,
                        'name' => $productName,
                        'service_type' => 'Medicine',
                        'category_id' => $categoryId,
                        'brand_id' => !empty($p['brand_id']) && is_numeric($p['brand_id']) ? (int)$p['brand_id'] : null,
                        'quantity' => $totalQty,
                        'purchase_price' => $price,
                        'mrp' => $mrp > 0 ? $mrp : $price,
                        'unit' => $unitStr,
                        'medicine_unit' => $p['medicine_unit'] ?? ($p['pack'] ?? 'Box'),
                        'stock_value' => $totalQty * ($price ?: ($mrp ?: 0)),
                        'availablility' => 'in_stock',
                        'batch_no' => $batchNo,
                        'barcode' => 'MED' . mt_rand(1000000000, 9999999999),
                        'expiry_date' => $expDate,
                        'manufacture_date' => $mfgDate,
                        'gst_option' => $gstOption,
                        'pack' => $p['pack'] ?? null,
                        'hsn_code' => $p['hsn_code'] ?? null,
                        'branch_id' => $branchId,
                        'status' => 'active',
                    ]);
                } else {
                    // Update stock and pricing
                    $currentQty = (int) $med->quantity;
                    $newQty = $currentQty + $totalQty;
                    $med->quantity = $newQty;
                    $med->availablility = 'in_stock';
                    if ($price > 0) $med->purchase_price = $price;
                    if ($mrp > 0) {
                        $med->mrp = $mrp;
                        $med->unit = $mrp;
                    }
                    if (!empty($p['batch_no'])) $med->batch_no = $p['batch_no'];
                    if (!empty($p['exp_date'])) $med->expiry_date = date('Y-m-d', strtotime($p['exp_date']));
                    if (!empty($p['pack'])) $med->pack = $p['pack'];
                    if (!empty($p['hsn_code'])) $med->hsn_code = $p['hsn_code'];
                    $med->stock_value = $newQty * ((float) ($med->purchase_price ?: $med->mrp ?: 0));
                    $med->save();
                }

                Purchases::create([
                    'item' => (string) $med->id,
                    'quantity' => $qty,
                    'batch_no' => $p['batch_no'] ?? null,
                    'brand_id' => $p['brand_id'] ?? null,
                    'mrp' => $mrp,
                    'mfg_date' => $p['mfg_date'] ?? null,
                    'exp_date' => $p['exp_date'] ?? null,
                    'free_quantity' => $freeQty,
                    'product_gst_details' => $p['product_gst_details'] ?? null,
                    'product_gst_total' => $p['product_gst_total'] ?? 0,
                    'price' => $price,
                    'scheme_free_qty' => $p['scheme_free_qty'] ?? 0,
                    'scheme_rate' => $p['scheme_rate'] ?? 0,
                    'discount_percent' => $p['discount_percent'] ?? 0,
                    'discount_amount' => $p['discount_amount'] ?? 0,
                    'purchase_date' => $request->purchase_date ?? now()->toDateString(),
                    'purchase_status' => 'completed',
                    'payment_status' => $remaining <= 0 ? 'paid' : 'pending',
                    'amount_total' => $total,
                    'vendor_id' => $vendorId,
                    'invoice_id' => (string) $purchaseInvoice->id,
                    'branch_id' => $branchId,
                    'created_by' => $userId,
                    'isDeleted' => 0,
                ]);
            }

            // 5. Record Payment Store
            if ($paid > 0) {
                PaymentStore::create([
                    'user_id' => $vendorId,
                    'invoice_id' => $purchaseInvoice->id,
                    'payment_amount' => $paid,
                    'payment_date' => now()->toDateString(),
                    'payment_type' => 'purchase',
                    'payment_method' => $request->payment_mode ?? 'cash',
                    'cash_amount' => in_array($request->payment_mode, ['cash', 'cash_online']) ? $paid : 0,
                    'upi_amount' => in_array($request->payment_mode, ['upi', 'online', 'cash_online']) ? $paid : 0,
                    'remaining_amount' => (string) $remaining,
                    'remarks' => $request->remarks,
                    'isDeleted' => 0,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => true,
                'message' => 'Purchase saved successfully and inventory stock updated!',
                'purchase_id' => $purchaseInvoice->id,
                'invoice_id' => $purchaseInvoice->id,
                'invoice_number' => $purchaseInvoice->invoice_number,
                'invoice_url' => url('purchase-invoice/' . $purchaseInvoice->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => false,
                'message' => 'Error saving purchase: ' . $e->getMessage()
            ], 500);
        }
    }

    public function purchase_list(Request $request)
    {
        $branchId = session('selectedBranchId') ?? Auth::user()->branch_id ?? 1;

        $purchases = PurchaseInvoice::with(['vendor'])
            ->where('isDeleted', 0)
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            })
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'purchases' => $purchases
        ]);
    }

    public function showPurchase($id)
    {
        $invoice = PurchaseInvoice::with(['vendor', 'purchases.medicine'])->find($id);

        if (!$invoice) {
            return response()->json(['status' => false, 'message' => 'Purchase invoice not found'], 404);
        }

        return response()->json([
            'status' => true,
            'invoice' => $invoice
        ]);
    }

    public function purchase_delete(Request $request)
    {
        $id = $request->id;
        $invoice = PurchaseInvoice::with('purchases')->find($id);

        if (!$invoice) {
            return response()->json(['status' => false, 'message' => 'Invoice not found'], 404);
        }

        try {
            DB::beginTransaction();

            // Reverse stock
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

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Purchase invoice deleted and inventory stock reversed.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error deleting purchase: ' . $e->getMessage()
            ], 500);
        }
    }
}

