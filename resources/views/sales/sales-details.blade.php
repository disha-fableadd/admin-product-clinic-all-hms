@extends('layout.app')

@section('title', 'Sale Details - #' . ($order->order_number ?? $order->id))

@section('content')
<style>
    .detail-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.05);
        padding: 30px;
        margin-bottom: 25px;
        border: 1px solid #eef2f6;
    }
    .pill-btn {
        border-radius: 50px !important;
        font-weight: 600;
        padding: 8px 22px;
    }
    .btn-clinic-primary {
        background-color: #f89884 !important;
        border-color: #f89884 !important;
        color: #fff !important;
    }
    .table th {
        background: #f8fafc;
        color: #475569;
        font-size: 13px;
        font-weight: 700;
        padding: 12px 14px;
    }
    .table td {
        font-size: 13px;
        padding: 12px 14px;
        vertical-align: middle;
    }
</style>

<div class="page-wrapper">
    <div class="content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1" style="font-weight: 700; color: #1e293b;">
                Sale Details <span style="color: #f89884;">#{{ $order->order_number ?? $order->id }}</span>
            </h3>
            <p class="text-muted mb-0" style="font-size: 14px;">Detailed transaction record and payments</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sales.list') }}" class="btn btn-outline-secondary pill-btn">
                <i class="fa fa-arrow-left me-1"></i> Back to Bills
            </a>
            <a href="{{ route('sales.invoice', $order->id) }}" class="btn btn-clinic-primary pill-btn">
                <i class="fa fa-print me-1"></i> View Invoice
            </a>
        </div>
    </div>

    @php
        $patient = $order->patients->first();
        $patientName = $patient ? ($patient->fullname ?? $patient->name) : ($order->patient_name ?? 'Walk-in Patient');
        $patientPhone = $patient ? ($patient->phone ?? $patient->phone_number) : ($order->patient_phone ?? '-');
        $isPaid = strtolower($order->payment_status) == 'paid' || ($order->remaining_amount <= 0 && $order->total_amount > 0);
    @endphp

    <div class="row">
        <!-- Patient & Order Meta -->
        <div class="col-md-4">
            <div class="detail-card">
                <h5 style="color: #f89884; font-weight: 700; margin-bottom: 16px;">
                    <i class="fa fa-user me-2"></i> Patient Information
                </h5>
                <ul class="list-unstyled mb-0" style="font-size: 14px; line-height: 2;">
                    <li><strong>Name:</strong> {{ $patientName }}</li>
                    <li><strong>Phone:</strong> {{ $patientPhone }}</li>
                    <li><strong>Age / Gender:</strong> {{ $patient->age ?? ($order->patient_age ?? '-') }} / {{ ucfirst($patient->gender ?? ($order->patient_sex ?? '-')) }}</li>
                    <li><strong>Bill Date:</strong> {{ date('d-M-Y', strtotime($order->order_date ?? $order->created_at)) }}</li>
                    <li><strong>GST Option:</strong> {{ strtoupper($order->gst_option ?? 'GST') }}</li>
                </ul>
            </div>

            <div class="detail-card">
                <h5 style="color: #1f513f; font-weight: 700; margin-bottom: 16px;">
                    <i class="fa fa-heartbeat me-2"></i> Clinical Record
                </h5>
                <ul class="list-unstyled mb-0" style="font-size: 13px; line-height: 1.8;">
                    <li><strong>BP:</strong> {{ $order->bp ?? ($patient->bp ?? 'Not recorded') }}</li>
                    <li><strong>Temp:</strong> {{ $order->temperature ?? ($patient->temperature ?? 'Not recorded') }}</li>
                    <li><strong>Pulse:</strong> {{ $order->pulse ?? ($patient->pulse ?? 'Not recorded') }}</li>
                    <li><strong>Complaint:</strong> {{ $order->patient_complaint ?? ($patient->patient_complaint ?? '-') }}</li>
                    <li><strong>History:</strong> {{ $order->patient_history ?? ($patient->patient_history ?? '-') }}</li>
                    <li><strong>Advice:</strong> {{ $order->advice_for_lab_tests ?? ($patient->advice_for_lab_tests ?? '-') }}</li>
                </ul>
            </div>
        </div>

        <!-- Items & Payment Breakdown -->
        <div class="col-md-8">
            <div class="detail-card">
                <h5 style="color: #1e293b; font-weight: 700; margin-bottom: 16px;">
                    <i class="fa fa-pills me-2" style="color: #f89884;"></i> Ordered Medicines
                </h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Medicine</th>
                                <th>Batch</th>
                                <th>Expiry</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @forelse($order->order_items as $item)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td><strong>{{ $item->product ? $item->product->name : ($item->custom_product_name ?? 'Medicine') }}</strong></td>
                                    <td>{{ $item->batch_number ?? ($item->batch_no ?? '-') }}</td>
                                    <td>{{ $item->expiry_date ?? '-' }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">₹{{ number_format((float)($item->price ?? ($item->unit_price ?? ($item->mrp ?? 0))), 2) }}</td>
                                    <td class="text-end font-weight-bold">₹{{ number_format((float)($item->total_amount ?? ($item->subtotal ?? (($item->quantity ?? 1) * ($item->price ?? 0)))), 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted">No items in this order</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Financial Totals -->
                <div class="row justify-content-end mt-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px !important;">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Grand Total:</span>
                                <strong>₹{{ number_format((float)$order->total_amount, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Total Paid:</span>
                                <strong>₹{{ number_format((float)($order->paid_amount ?? ($order->payment_status == 'paid' ? $order->total_amount : max(0, $order->total_amount - ($order->remaining_amount ?? 0)))), 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between text-danger">
                                <span>Remaining Due:</span>
                                <strong>₹{{ number_format((float)($order->remaining_amount ?? 0), 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Transactions -->
            <div class="detail-card">
                <h5 style="color: #1e293b; font-weight: 700; margin-bottom: 16px;">
                    <i class="fa fa-history me-2" style="color: #f89884;"></i> Payment History
                </h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Payment Mode</th>
                                <th>Amount (₹)</th>
                                <th>Notes / Ref</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->payments as $payment)
                                <tr>
                                    <td>{{ date('d-M-Y', strtotime($payment->payment_date ?? $payment->created_at)) }}</td>
                                    <td>{{ ucfirst($payment->payment_method ?? ($payment->payment_mode ?? 'Cash')) }}</td>
                                    <td><strong class="text-success">₹{{ number_format((float)($payment->payment_amount ?? ($payment->amount ?? 0)), 2) }}</strong></td>
                                    <td>{{ $payment->remarks ?? ($payment->notes ?? '-') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td>{{ date('d-M-Y', strtotime($order->order_date ?? $order->created_at)) }}</td>
                                    <td>{{ ucfirst($order->payment_method ?? 'Cash') }}</td>
                                    <td><strong class="text-success">₹{{ number_format(max(0, (float)$order->total_amount - (float)($order->remaining_amount ?? 0)), 2) }}</strong></td>
                                    <td>Initial Bill Payment</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
