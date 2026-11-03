@extends('layout.app')

@section('title', 'Purchase Details - #' . ($invoice->bill_no ?? $invoice->invoice_number))

@section('content')
<style>
    .detail-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.05);
        padding: 26px;
        margin-bottom: 22px;
        border: 1px solid #eef2f6;
    }
    .pill-btn {
        border-radius: 50px !important;
        font-weight: 600;
        padding: 8px 22px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .btn-clinic-primary {
        background-color: #f89884 !important;
        border: none !important;
        color: #fff !important;
    }
    .btn-clinic-primary:hover {
        background-color: #f6816a !important;
        color: #fff !important;
    }
    .table th {
        background: #f8fafc;
        color: #475569;
        font-size: 13px;
        font-weight: 700;
        padding: 12px 14px;
        border-bottom: 2px solid #e2e8f0;
    }
    .table td {
        font-size: 13px;
        padding: 12px 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .info-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
    }
    .info-val {
        font-size: 13.5px;
        color: #1e293b;
        font-weight: 600;
    }
    .page-wrapper {
        padding-top: 70px !important;
    }
    .page-wrapper > .content,
    .content {
        padding-top: 22px !important;
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="mb-1" style="font-weight: 700; color: #1e293b;">
                    Purchase Details <span style="color: #f89884;">#{{ $invoice->bill_no ?? $invoice->invoice_number }}</span>
                </h3>
                <p class="text-muted mb-0" style="font-size: 14px;">Detailed vendor purchase bill and payment history</p>
            </div>
            <div class="d-flex gap-2" style="gap:10px">
                <a href="{{ route('purchase.lists') }}" class="btn btn-outline-secondary pill-btn" style="border-radius: 50px !important;">
                    <i class="fas fa-arrow-left"></i> Back to Bills
                </a>
                <a href="{{ route('purchase.invoice', $invoice->id) }}" class="btn btn-clinic-primary pill-btn">
                    <i class="fas fa-file-invoice"></i> View Invoice
                </a>
                <a href="{{ route('purchase.edit', $invoice->id) }}" class="btn pill-btn" style="background-color: #cfece0; color: #1f513f; border: none;">
                    <i class="fas fa-edit"></i> Edit Bill
                </a>
            </div>
        </div>

        <div class="row g-3">
            <!-- Vendor Information -->
            <div class="col-lg-5 col-12">
                <div class="detail-card h-100">
                    <h5 class="mb-3 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-handshake" style="color: #f89884;"></i> Vendor Information
                    </h5>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="info-label">Vendor Name:</span>
                            <span class="info-val">{{ optional($invoice->vendor)->name ?? ($invoice->vendor_id ?: 'Unknown Vendor') }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="info-label">Phone:</span>
                            <span class="info-val">{{ optional($invoice->vendor)->phone ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="info-label">Email:</span>
                            <span class="info-val">{{ optional($invoice->vendor)->email ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="info-label">GST Number:</span>
                            <span class="info-val">{{ optional($invoice->vendor)->gst_number ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="info-label">Address:</span>
                            <span class="info-val">{{ optional($invoice->vendor)->address ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="info-label">Bill Date:</span>
                            <span class="info-val">{{ $invoice->purchase_date ? date('d-M-Y', strtotime($invoice->purchase_date)) : ($invoice->created_at ? $invoice->created_at->format('d-M-Y') : '-') }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="info-label">Due Date:</span>
                            <span class="info-val">{{ $invoice->due_date ? date('d-M-Y', strtotime($invoice->due_date)) : '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="info-label">GST Option:</span>
                            <span class="badge bg-light text-dark" style="border-radius: 50px;">{{ strtoupper(str_replace('_', ' ', $invoice->gst_option ?? 'WITHOUT_GST')) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="col-lg-7 col-12">
                <div class="detail-card h-100">
                    <h5 class="mb-3 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-file-invoice-dollar" style="color: #28c76f;"></i> Billing & Balance Summary
                    </h5>
                    <div class="row g-3">
                        <div class="col-sm-4 col-12">
                            <div class="p-3 rounded-4" style="background: #f8fafc; border: 1.5px solid #e2e8f0;">
                                <span class="text-muted" style="font-size: 12px; font-weight: 600;">Subtotal</span>
                                <h4 class="mb-0 mt-1 fw-bold text-dark">₹{{ number_format($invoice->total_amount ?? 0, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-4 col-12">
                            <div class="p-3 rounded-4" style="background: #f0fff4; border: 1.5px solid #28c76f;">
                                <span class="text-muted" style="font-size: 12px; font-weight: 600;">Total Paid</span>
                                <h4 class="mb-0 mt-1 fw-bold text-success">₹{{ number_format($invoice->paid ?? 0, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-4 col-12">
                            <div class="p-3 rounded-4" style="background: #fff5f5; border: 1.5px solid #ea5455;">
                                <span class="text-muted" style="font-size: 12px; font-weight: 600;">Remaining Due</span>
                                <h4 class="mb-0 mt-1 fw-bold text-danger">₹{{ number_format($invoice->remaining_amount ?? 0, 2) }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-top d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between">
                            <span class="info-label">Shipping Charges:</span>
                            <span class="info-val">₹{{ number_format($invoice->shipping ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="info-label">Interest Rate (Before Due / After Due):</span>
                            <span class="info-val">{{ $invoice->interest_type ?? '0' }}% / {{ $invoice->interest_amount ?? '0' }}%</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="info-label">Interest Amount:</span>
                            <span class="info-val">₹{{ number_format($invoice->interest_amount ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top">
                            <span class="fw-bold text-dark fs-6">Grand Total / Final Payable:</span>
                            <span class="fw-bold fs-5" style="color: #f89884;">₹{{ number_format($invoice->final_amount_after_interest ?? $invoice->grand_total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ordered Medicines / Items -->
            <div class="col-12 mt-4">
                <div class="detail-card">
                    <h5 class="mb-3 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-pills" style="color: #f89884;"></i> Purchased Medicines & Items
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Medicine Name</th>
                                    <th>Batch No</th>
                                    <th>Expiry Date</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Purchase Rate</th>
                                    <th class="text-center">Discount</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoice->purchases as $idx => $pItem)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="fw-semibold text-dark">
                                            {{ optional($pItem->medicine)->name ?? ($pItem->item ?: 'Medicine Item') }}
                                            @if(optional($pItem->medicine)->quantity !== null)
                                                <br><small class="text-muted"><i class="fas fa-boxes me-1"></i>Current Stock: <strong>{{ optional($pItem->medicine)->quantity }}</strong></small>
                                            @endif
                                        </td>
                                        <td>{{ $pItem->batch_no ?? '-' }}</td>
                                        <td>{{ $pItem->exp_date ? date('d-M-Y', strtotime($pItem->exp_date)) : '-' }}</td>
                                        <td class="text-center fw-bold">
                                            {{ $pItem->quantity }}
                                            @if((int)($pItem->free_quantity ?? 0) > 0)
                                                <br><small class="text-success fw-semibold">(+{{ $pItem->free_quantity }} Free)</small>
                                            @endif
                                        </td>
                                        <td class="text-end">₹{{ number_format($pItem->price ?? 0, 2) }}</td>
                                        <td class="text-center">{{ $pItem->discount_percent ? ($pItem->discount_percent . '%') : '-' }}</td>
                                        <td class="text-end fw-bold">₹{{ number_format($pItem->amount_total ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-3 text-muted">No line items recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="col-12">
                <div class="detail-card">
                    <h5 class="mb-3 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-history" style="color: #5a6a80;"></i> Payment History
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Payment Date</th>
                                    <th>Method</th>
                                    <th class="text-end">Amount Paid</th>
                                    <th class="text-end">Remaining Balance</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoice->payments as $k => $pm)
                                    <tr>
                                        <td>{{ $k + 1 }}</td>
                                        <td>{{ $pm->payment_date ? date('d-M-Y', strtotime($pm->payment_date)) : ($pm->created_at ? $pm->created_at->format('d-M-Y') : '-') }}</td>
                                        <td><span class="badge bg-light text-dark" style="border-radius: 50px;">{{ strtoupper($pm->payment_method ?? 'CASH') }}</span></td>
                                        <td class="text-end fw-bold text-success">₹{{ number_format($pm->payment_amount ?? 0, 2) }}</td>
                                        <td class="text-end fw-bold text-danger">₹{{ number_format($pm->remaining_amount ?? 0, 2) }}</td>
                                        <td>{{ $pm->remarks ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">No payment records found.</td>
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

