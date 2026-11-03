<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Medicine;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Patients;
use App\Models\TaxRate;
use App\Models\BankMaster;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function sales_list(Request $request)
    {
        $branchId = session('selectedBranchId') ?? Auth::user()->branch_id ?? 1;

        $query = Order::with(['order_items.product', 'patients', 'payments'])
            ->where('isDeleted', 0);

        if ($branchId) {
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }

        $orders = $query->orderBy('id', 'desc')->get();

        $patientQuery = Patients::query();
        if ($branchId) {
            $patientQuery->where(function($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }
        $patients = $patientQuery->orderBy('fullname', 'asc')->get();

        $banks = BankMaster::where('status', 1)->where('isDeleted', 0)->get();

        $years = Order::where('isDeleted', 0)
            ->selectRaw('DISTINCT YEAR(order_date) as yr')
            ->whereNotNull('order_date')
            ->orderBy('yr', 'desc')
            ->pluck('yr')
            ->toArray();
        if (empty($years)) {
            $years = [date('Y')];
        }

        $totalSales = $orders->sum('total_amount');
        $totalPaid = $orders->where('payment_status', 'paid')->sum('total_amount');
        $totalRemaining = $orders->sum('remaining_amount');

        return view('sales.saleslist', compact('orders', 'patients', 'banks', 'years', 'totalSales', 'totalPaid', 'totalRemaining'));
    }

    public function pos(Request $request)
    {
        $branchId = session('selectedBranchId') ?? Auth::user()->branch_id ?? 1;

        $categories = Category::where('branch_id', $branchId)->orWhereNull('branch_id')->get();
        $brands = Brand::where('branch_id', $branchId)->orWhereNull('branch_id')->get();
        $taxes = TaxRate::where('status', 'active')->where('isDeleted', 0)->get();
        $patients = Patients::orderBy('id', 'desc')->get();
        $banks = BankMaster::where('status', 1)->where('isDeleted', 0)->get();
        $medicines = Medicine::orderBy('name', 'asc')->get();

        $stockUnits = \App\Models\StockUnit::all();
        return view('sales.add-sales', compact('categories', 'brands', 'taxes', 'patients', 'banks', 'medicines', 'stockUnits'));
    }

    public function add_sales(Request $request)
    {
        return $this->pos($request);
    }

    public function salse_invoice($id)
    {
        $order = Order::with(['order_items.product', 'patients', 'payments'])->findOrFail($id);
        $setting = Setting::first();
        return view('sales.salse-invoice', compact('order', 'setting'));
    }

    public function sales_details($id)
    {
        $order = Order::with(['order_items.product', 'patients', 'payments'])->findOrFail($id);
        return view('sales.sales-details', compact('order'));
    }

    public function edit_sales($id)
    {
        $order = Order::with(['order_items.product', 'patients', 'payments'])->findOrFail($id);
        $branchId = session('selectedBranchId') ?? Auth::user()->branch_id ?? 1;
        $categories = Category::where('branch_id', $branchId)->orWhereNull('branch_id')->get();
        $brands = Brand::where('branch_id', $branchId)->orWhereNull('branch_id')->get();
        $taxes = TaxRate::where('status', 'active')->where('isDeleted', 0)->get();
        $patients = Patients::orderBy('id', 'desc')->get();
        $banks = BankMaster::where('status', 1)->where('isDeleted', 0)->get();
        $medicines = Medicine::orderBy('name', 'asc')->get();

        return view('sales.edit-sales', compact('order', 'categories', 'brands', 'taxes', 'patients', 'banks', 'medicines'));
    }

    public function update_sales_payment(Request $request, $id)
    {
        $order = Order::with('order_items')->findOrFail($id);
        $branchId = session('selectedBranchId') ?? optional(Auth::user())->branch_id ?? $order->branch_id ?? 1;
        $userId = optional(Auth::user())->id ?? 1;

        // 1. Update / Resolve Patient Vitals & Details
        $patientId = $request->patient_id ?? $order->user_id;
        if (!empty($patientId) && is_numeric($patientId)) {
            $patient = Patients::find($patientId);
            if ($patient) {
                if ($request->filled('patient_phone')) $patient->phone = $request->patient_phone;
                if ($request->filled('patient_age')) $patient->age = $request->patient_age;
                if ($request->filled('patient_sex')) $patient->gender = $request->patient_sex;
                if ($request->filled('patient_bp')) $patient->bp = $request->patient_bp;
                if ($request->filled('patient_temperature')) $patient->temperature = $request->patient_temperature;
                if ($request->filled('patient_pulse')) $patient->pulse = $request->patient_pulse;
                if ($request->filled('patient_history')) $patient->patient_history = $request->patient_history;
                if ($request->filled('patient_complaint')) $patient->patient_complaint = $request->patient_complaint;
                if ($request->filled('advice_for_lab_tests')) $patient->advice_for_lab_tests = $request->advice_for_lab_tests;
                $patient->save();

                $order->user_id = $patient->id;

                // Update pivot
                DB::table('order_patient')->where('order_id', $order->id)->delete();
                DB::table('order_patient')->insert([
                    'order_id' => $order->id,
                    'patient_id' => $patient->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // 2. Update Order Items & Stock (if items are passed)
        if ($request->has('items') && is_array($request->items) && count($request->items) > 0) {
            // Revert previous items' stock
            $oldItems = OrderItem::where('order_id', $order->id)->get();
            foreach ($oldItems as $oldItem) {
                if ($oldItem->product_id) {
                    $med = Medicine::find($oldItem->product_id);
                    if ($med) {
                        $med->increment('quantity', (int)$oldItem->quantity);
                    }
                }
            }
            OrderItem::where('order_id', $order->id)->delete();

            // Create new order items & decrement stock
            foreach ($request->items as $item) {
                $qty = (int) ($item['quantity'] ?? 1);
                $price = (float) ($item['price'] ?? 0);
                $mrp = (float) ($item['mrp'] ?? $price);
                $total = (float) ($item['total'] ?? ($qty * $price));

                $productId = !empty($item['product_id']) ? (int) $item['product_id'] : null;
                $isCustom = !empty($item['is_custom']) || !$productId;
                $customName = $item['name'] ?? null;

                $gstDetails = $item['product_gst_details'] ?? null;
                if (is_string($gstDetails)) {
                    $gstDetails = json_decode($gstDetails, true);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'user_id' => $patientId ?? $order->user_id,
                    'product_id' => $productId,
                    'custom_product_name' => $customName,
                    'is_custom_product' => $isCustom ? 1 : 0,
                    'batch_number' => $item['batch_no'] ?? null,
                    'expiry_date' => !empty($item['expiry_date']) ? date('Y-m-d', strtotime($item['expiry_date'])) : null,
                    'product_gst_details' => $gstDetails,
                    'product_gst_total' => $item['product_gst_total'] ?? 0,
                    'product_discount' => $item['discount_percent'] ?? 0,
                    'product_discount_price' => $item['discount_price'] ?? 0,
                    'quantity' => $qty,
                    'price' => $price,
                    'mrp' => $mrp,
                    'total_amount' => $total,
                    'branch_id' => $order->branch_id ?? $branchId,
                    'created_by' => $userId,
                    'sale_type' => 'local',
                    'isDeleted' => 0,
                ]);

                if ($productId) {
                    $med = Medicine::find($productId);
                    if ($med) {
                        $med->decrement('quantity', $qty);
                    }
                }
            }
        }

        // 3. Update Order Totals, Dates, and Charges
        if ($request->filled('order_date')) {
            $order->order_date = $request->order_date;
        }
        if ($request->filled('shipping')) {
            $order->shipping = $request->shipping;
        }
        if ($request->filled('misc_charges')) {
            $order->misc_charges = $request->misc_charges;
        }
        if ($request->filled('discount')) {
            $order->discount = $request->discount;
        }
        if ($request->filled('remarks')) {
            $order->remarks = $request->remarks;
        }
        if ($request->filled('gst_option')) {
            $order->gst_option = $request->gst_option;
        }

        $totalAmount = (float) ($request->total ?? $request->total_amount ?? $order->total_amount);
        $order->total_amount = $totalAmount;

        // 4. Payment Calculations
        $paymentMethod = strtolower($request->payment_method ?? $order->payment_method ?? 'cash');
        $paidType = strtolower($request->paid_type ?? 'full');

        $cashAmount = (float) ($request->cash_amount ?? 0);
        $onlineAmount = (float) ($request->online_amount ?? ($request->upi_amount ?? 0));
        $enterAmount = (float) ($request->enter_amount ?? ($request->amount ?? 0));

        if (in_array($paymentMethod, ['pay_later', 'pending', 'credit'])) {
            $paidAmount = 0.00;
            $cashAmount = 0.00;
            $onlineAmount = 0.00;
        } elseif (in_array($paymentMethod, ['cash+online', 'cash_online', 'both'])) {
            if ($paidType === 'full' && ($cashAmount + $onlineAmount) <= 0) {
                $paidAmount = $totalAmount;
                $cashAmount = $totalAmount;
                $onlineAmount = 0.00;
            } else {
                $paidAmount = min($totalAmount, $cashAmount + $onlineAmount);
            }
        } else {
            if ($paidType === 'full') {
                $paidAmount = $totalAmount;
            } else {
                $paidAmount = min($totalAmount, $enterAmount > 0 ? $enterAmount : (float)($request->paid_amount ?? 0));
            }
            if ($paymentMethod === 'cash') {
                $cashAmount = $paidAmount;
                $onlineAmount = 0.00;
            } else {
                $onlineAmount = $paidAmount;
                $cashAmount = 0.00;
            }
        }

        $remainingAmount = max(0, $totalAmount - $paidAmount);
        $paymentStatus = $remainingAmount <= 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'pending');

        $order->payment_method = $paymentMethod;
        $order->remaining_amount = $remainingAmount;
        $order->payment_status = $paymentStatus;
        if ($request->bank_id) {
            $order->bank_id = $request->bank_id;
        }
        $order->save();

        if ($paidAmount > 0) {
            \App\Models\PaymentStore::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'payment_amount' => $paidAmount,
                'payment_date' => now()->toDateString(),
                'payment_type' => 'order',
                'payment_method' => $paymentMethod,
                'cash_amount' => $cashAmount,
                'upi_amount' => $onlineAmount,
                'remaining_amount' => (string) $remainingAmount,
                'remarks' => 'Payment updated via Edit Sale',
                'isDeleted' => 0,
            ]);
        }

        $invoiceUrl = route('sales.invoice', $order->id);
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Sale bill updated successfully!',
                'order_id' => $order->id,
                'invoice_url' => $invoiceUrl,
                'redirect' => $invoiceUrl
            ]);
        }

        return redirect($invoiceUrl)->with('success', 'Sale bill updated successfully!');
    }
}
