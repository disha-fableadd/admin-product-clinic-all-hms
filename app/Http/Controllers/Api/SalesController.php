<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Medicine;
use App\Models\Patients;
use App\Models\PaymentStore;
use App\Models\ProductDocumentation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{
    public function order_sale(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
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
            $userId = optional(Auth::user())->id;

            // 1. Resolve / Create Patient & Vitals
            $patientId = null;
            if (!empty($request->patient_id) && is_numeric($request->patient_id)) {
                $patient = Patients::find($request->patient_id);
                if ($patient) {
                    $patientId = $patient->id;
                    // Update vitals if passed
                    if ($request->filled('patient_bp')) $patient->bp = $request->patient_bp;
                    if ($request->filled('patient_temperature')) $patient->temperature = $request->patient_temperature;
                    if ($request->filled('patient_pulse')) $patient->pulse = $request->patient_pulse;
                    if ($request->filled('patient_weight')) $patient->weight = $request->patient_weight;
                    if ($request->filled('patient_height')) $patient->height = $request->patient_height;
                    if ($request->filled('patient_history')) $patient->patient_history = $request->patient_history;
                    if ($request->filled('patient_complaint')) $patient->patient_complaint = $request->patient_complaint;
                    if ($request->filled('advice_for_lab_tests')) $patient->advice_for_lab_tests = $request->advice_for_lab_tests;
                    if ($request->filled('patient_age')) $patient->age = $request->patient_age;
                    if ($request->filled('patient_sex')) $patient->gender = $request->patient_sex;
                    $patient->save();
                }
            } elseif (!empty($request->patient_name)) {
                // Quick create patient
                $patient = Patients::create([
                    'fullname' => $request->patient_name,
                    'phone' => $request->patient_phone ?? null,
                    'age' => $request->patient_age ?? null,
                    'gender' => $request->patient_sex ?? 'Male',
                    'bp' => $request->patient_bp ?? null,
                    'temperature' => $request->patient_temperature ?? null,
                    'pulse' => $request->patient_pulse ?? null,
                    'weight' => $request->patient_weight ?? null,
                    'height' => $request->patient_height ?? null,
                    'patient_history' => $request->patient_history ?? null,
                    'patient_complaint' => $request->patient_complaint ?? null,
                    'advice_for_lab_tests' => $request->advice_for_lab_tests ?? null,
                    'branch_id' => $branchId,
                ]);
                $patientId = $patient->id;
            }

            // 2. Generate unique order / invoice number
            $latestId = (Order::max('id') ?? 0) + 1;
            $orderNumber = 'INV-' . str_pad($latestId, 6, '0', STR_PAD_LEFT);

            $paymentMethod = strtolower($request->payment_method ?? 'cash');
            $totalAmount = (float) $request->total;
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

            // 3. Create Order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $patientId,
                'order_date' => $request->order_date ?? now()->toDateString(),
                'order_type' => $request->order_type ?? 'sale',
                'discount' => $request->discount ?? 0,
                'discount_percent' => $request->overall_discount_percent ?? null,
                'discount_amount' => $request->overall_discount_amount ?? null,
                'round_off' => $request->round_off ?? 0,
                'shipping' => $request->shipping ?? 0,
                'misc_charges' => $request->misc_charges ?? 0,
                'gst_option' => $request->gst_option ?? 'without_gst',
                'branch_id' => $branchId,
                'created_by' => $userId,
                'total_amount' => $totalAmount,
                'remaining_amount' => $remainingAmount,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'order_invoice' => $orderNumber,
                'remarks' => $request->remarks ?? null,
                'is_emergency' => $request->is_emergency ?? 0,
                'isDeleted' => 0,
            ]);

            // 4. Attach Patient pivot
            if ($patientId) {
                DB::table('order_patient')->updateOrInsert(
                    ['order_id' => $order->id, 'patient_id' => $patientId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }

            // 5. Create Order Items & Decrement Medicine Stock
            foreach ($request->items as $item) {
                $qty = (int) ($item['quantity'] ?? 1);
                $price = (float) ($item['price'] ?? 0);
                $mrp = (float) ($item['mrp'] ?? $price);
                $total = (float) ($item['total'] ?? ($qty * $price));

                $productId = !empty($item['product_id']) ? (int) $item['product_id'] : null;
                $isCustom = !empty($item['is_custom']) || !$productId;
                $customName = $item['custom_name'] ?? ($item['name'] ?? null);

                OrderItem::create([
                    'order_id' => $order->id,
                    'user_id' => $patientId,
                    'product_id' => $productId,
                    'custom_product_name' => $customName,
                    'is_custom_product' => $isCustom ? 1 : 0,
                    'batch_number' => $item['batch_no'] ?? null,
                    'expiry_date' => !empty($item['expiry_date']) ? date('Y-m-d', strtotime($item['expiry_date'])) : null,
                    'product_gst_details' => $item['product_gst_details'] ?? null,
                    'product_gst_total' => $item['product_gst_total'] ?? 0,
                    'product_discount' => $item['discount_percent'] ?? 0,
                    'product_discount_price' => $item['discount_price'] ?? 0,
                    'category_id' => $item['category_id'] ?? null,
                    'quantity' => $qty,
                    'price' => $price,
                    'mrp' => $mrp,
                    'total_amount' => $total,
                    'branch_id' => $branchId,
                    'created_by' => $userId,
                    'sale_type' => $item['sale_type'] ?? 'local',
                    'isDeleted' => 0,
                ]);

                // Decrement inventory stock
                if ($productId) {
                    $med = Medicine::find($productId);
                    if ($med) {
                        $newStock = max(0, ((int) $med->quantity) - $qty);
                        $med->quantity = $newStock;
                        $med->availablility = $newStock > 0 ? 'in_stock' : 'out_stock';
                        $med->stock_value = $newStock * ((float) ($med->purchase_price ?: $med->mrp ?: $med->unit ?: 0));
                        $med->save();
                    }
                }
            }

            // 6. Record Payment
            if ($paidAmount > 0) {
                PaymentStore::create([
                    'user_id' => $patientId,
                    'order_id' => $order->id,
                    'payment_amount' => $paidAmount,
                    'payment_date' => now()->toDateString(),
                    'payment_type' => 'order',
                    'payment_method' => $paymentMethod,
                    'cash_amount' => $cashAmount,
                    'upi_amount' => $onlineAmount,
                    'remaining_amount' => (string) $remainingAmount,
                    'remarks' => $request->remarks,
                    'isDeleted' => 0,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Sale created successfully!',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'invoice_url' => url('sales-invoice/' . $order->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error saving sale: ' . $e->getMessage()
            ], 500);
        }
    }

    public function get_orders(Request $request)
    {
        $branchId = session('selectedBranchId') ?? Auth::user()->branch_id ?? 1;

        $orders = Order::with(['order_items.product', 'patients'])
            ->where('isDeleted', 0)
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            })
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'orders' => $orders
        ]);
    }

    public function getsalseById($id)
    {
        $order = Order::with(['order_items.product', 'patients', 'payments'])->find($id);

        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        return response()->json([
            'status' => true,
            'order' => $order
        ]);
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $order = Order::with('order_items')->findOrFail($id);

            // Restore Stock
            foreach ($order->order_items as $item) {
                if ($item->product_id) {
                    $med = Medicine::find($item->product_id);
                    if ($med) {
                        $med->quantity = ((int) $med->quantity) + ((int) $item->quantity);
                        $med->availablility = 'in_stock';
                        $med->stock_value = $med->quantity * ((float) ($med->purchase_price ?: $med->mrp ?: $med->unit ?: 0));
                        $med->save();
                    }
                }
            }

            $order->isDeleted = 1;
            $order->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Sale invoice deleted and inventory stock restored.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error deleting sale: ' . $e->getMessage()
            ], 500);
        }
    }

    public function makePaymentSubmit(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|string',
        ]);

        $order = Order::findOrFail($request->order_id);
        $paymentMethod = strtolower($request->payment_method ?? 'cash');
        $cashAmount = (float) ($request->cash_amount ?? 0);
        $onlineAmount = (float) ($request->online_amount ?? ($request->upi_amount ?? 0));
        $amount = (float) ($request->amount ?? $request->paid_amount ?? 0);

        if (in_array($paymentMethod, ['cash_online', 'cash+online', 'both'])) {
            $paymentMethod = 'cash_online';
            $amount = $cashAmount + $onlineAmount;
        } elseif ($paymentMethod === 'cash') {
            if ($amount <= 0 && $cashAmount > 0) $amount = $cashAmount;
            $cashAmount = $amount;
            $onlineAmount = 0;
        } else {
            if ($amount <= 0 && $onlineAmount > 0) $amount = $onlineAmount;
            $onlineAmount = $amount;
            $cashAmount = 0;
        }

        if ($amount <= 0) {
            return response()->json(['status' => false, 'message' => 'Please enter a valid amount.'], 422);
        }

        $newRemaining = max(0, ((float) $order->remaining_amount) - $amount);
        $order->remaining_amount = $newRemaining;
        $order->payment_status = $newRemaining <= 0 ? 'paid' : 'partial';
        if (!in_array($paymentMethod, ['pending'])) {
            $order->payment_method = $paymentMethod;
        }
        $order->save();

        $remarks = trim(($request->remarks ?? '') . ($request->received_by ? ' | Rec By: ' . $request->received_by : '') . ($request->paid_by ? ' | Paid By: ' . $request->paid_by : ''));

        PaymentStore::create([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'payment_amount' => $amount,
            'payment_date' => $request->payment_date ? date('Y-m-d', strtotime($request->payment_date)) : now()->toDateString(),
            'payment_type' => 'order',
            'payment_method' => $paymentMethod,
            'cash_amount' => $cashAmount,
            'upi_amount' => $onlineAmount,
            'remaining_amount' => (string) $newRemaining,
            'remarks' => $remarks ?: null,
            'isDeleted' => 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Payment recorded successfully!',
            'remaining_amount' => $newRemaining,
            'payment_status' => $order->payment_status,
        ]);
    }

    public function getDocumentation($order_id)
    {
        $documentation = ProductDocumentation::where('order_id', $order_id)->first();

        if ($documentation && $documentation->coa_pdfs) {
            $fullUrls = [];
            foreach ($documentation->coa_pdfs as $pdf) {
                $fullUrls[] = url('storage/' . $pdf);
            }
            $documentation->coa_pdfs = $fullUrls;
        }

        return response()->json([
            'status' => 'success',
            'data' => $documentation
        ]);
    }

    public function storeDocumentation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'batch_number' => 'nullable|string|max:255',
            'manufacturing_date' => 'nullable',
            'expiry_date' => 'nullable',
            'coa_file' => 'nullable|array',
            'coa_file.*' => 'file|mimes:pdf|max:15360',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $user = Auth::guard('api')->user() ?? Auth::user();

        $documentation = ProductDocumentation::where('order_id', $request->order_id)->first() ?: new ProductDocumentation();

        $documentation->order_id = $request->order_id;
        $documentation->batch_number = $request->batch_number;
        $documentation->mfg_date = $request->manufacturing_date ? (preg_match('/^\d{4}-\d{2}$/', $request->manufacturing_date) ? $request->manufacturing_date . '-01' : $request->manufacturing_date) : null;
        $documentation->expiry_date = $request->expiry_date ? (preg_match('/^\d{4}-\d{2}$/', $request->expiry_date) ? $request->expiry_date . '-01' : $request->expiry_date) : null;
        $documentation->created_by = $user ? $user->id : 1;

        if ($request->hasFile('coa_file')) {
            $coaPaths = $documentation->coa_pdfs ?? [];
            foreach ($request->file('coa_file') as $file) {
                $coaPaths[] = $file->store('product/documentations', 'public');
            }
            $documentation->coa_pdfs = $coaPaths;
        }

        $documentation->save();

        return response()->json(['status' => 'success', 'message' => 'Documentation saved successfully.']);
    }

    public function getPaymentHistory($order_id)
    {
        $order = Order::with('payments')->find($order_id);
        $payments = PaymentStore::where('order_id', $order_id)
            ->where('isDeleted', 0)
            ->orderBy('id', 'desc')
            ->get();

        $totalAmount = $order ? (float) $order->total_amount : 0;
        $remainingAmount = $order ? (float) $order->remaining_amount : 0;
        $totalPaid = max(0, $totalAmount - $remainingAmount);

        return response()->json([
            'status' => true,
            'order' => $order,
            'total_amount' => $totalAmount,
            'paid_amount' => $totalPaid,
            'return_amount' => 0.00,
            'remaining_amount' => $remainingAmount,
            'payments' => $payments
        ]);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['status' => false, 'message' => 'No sales selected.'], 400);
        }

        try {
            DB::beginTransaction();

            foreach ($ids as $id) {
                $order = Order::with('order_items')->find($id);
                if ($order && !$order->isDeleted) {
                    foreach ($order->order_items as $item) {
                        if ($item->product_id) {
                            $med = Medicine::find($item->product_id);
                            if ($med) {
                                $med->quantity = ((int) $med->quantity) + ((int) $item->quantity);
                                $med->availablility = 'in_stock';
                                $med->stock_value = $med->quantity * ((float) ($med->purchase_price ?: $med->mrp ?: $med->unit ?: 0));
                                $med->save();
                            }
                        }
                    }
                    $order->isDeleted = 1;
                    $order->save();
                }
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Selected sales deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}

