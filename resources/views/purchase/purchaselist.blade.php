@extends('layout.app')

@section('title', 'Purchase Bills List')

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<style>
    /* Clinic Project Theme */
    :root {
        --clinic-primary: #f89884;
        --clinic-primary-hover: #f6816a;
        --clinic-secondary: #cfece0;
        --clinic-dark: #1e293b;
        --clinic-slate: #5a6a80;
    }

    /* Card Header matching appointment, followup, and invoice */
    .card-header {
        background-color: #f89884 !important;
        padding: 14px 22px !important;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        border-top-left-radius: 18px !important;
        border-top-right-radius: 18px !important;
    }

    .btn-hdr {
        background-color: #fed9cf !important;
        color: #1e293b !important;
        font-weight: 600;
        font-size: 13px;
        border-radius: 50px !important;
        padding: 6px 18px !important;
        border: none !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }
    .btn-hdr:hover {
        background-color: #fbc2b3 !important;
        color: #000000 !important;
        transform: translateY(-1px);
    }

    /* 50% Border Radius Pills */
    .pill-btn {
        border-radius: 50px !important;
        font-weight: 600;
        padding: 7px 20px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    /* Form Controls & Select2 (50% Radius) */
    .form-control, .form-select, select.form-control,
    .select2-container .select2-selection--single {
        border-radius: 50px !important;
        height: 38px !important;
        border: 1.5px solid #cbd5e1 !important;
        padding: 4px 16px !important;
        font-size: 13px !important;
        background-color: #ffffff !important;
        color: #1e293b !important;
    }
    .form-control:focus, .form-select:focus,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #f89884 !important;
        box-shadow: 0 0 0 3px rgba(248, 152, 132, 0.25) !important;
        outline: none;
    }

    .select2-container .select2-selection--single {
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        padding-left: 0 !important;
        color: #1e293b !important;
        font-size: 13px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 12px !important;
    }

    /* KPI Summary Pills */
    .kpi-pill {
        border-radius: 50px !important;
        padding: 9px 22px;
        font-weight: 700;
        font-size: 13.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
        flex: 1 1 200px;
    }
    .kpi-pill-coral {
        border: 1.5px solid #f89884;
        background: #fff5f2;
        color: #d9534f;
    }
    .kpi-pill-green {
        border: 1.5px solid #28c76f;
        background: #f0fff4;
        color: #15803d;
    }
    .kpi-pill-pending {
        border: 1.5px solid #ea5455;
        background: #fff5f5;
        color: #dc2626;
    }

    /* Page Spacing Fix */
    .page-wrapper {
        padding-top: 70px !important;
    }
    .page-wrapper > .content,
    .content {
        padding-top: 22px !important;
    }

    /* Table styling matching Clinic Appointments & Invoices */
    .table-container-card {
        border-radius: 20px !important;
        border: 1.5px solid #e2e8f0;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        background: #ffffff;
        overflow: hidden !important;
    }

    .table-responsive {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        min-height: auto !important;
        padding-bottom: 0 !important;
        margin-bottom: 0 !important;
        width: 100% !important;
    }

        .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #f89884;
    }

    .custom-table {
        width: 100%;
        min-width: 950px;
        border-collapse: separate;
        border-spacing: 0;
    }
    .custom-table thead tr th {
        background-color: #f89884 !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 13px;
        padding: 12px 14px;
        border: none;
        white-space: nowrap;
        vertical-align: middle;
    }
    .custom-table thead tr th:first-child {
        border-top-left-radius: 12px;
    }
    .custom-table thead tr th:last-child {
        border-top-right-radius: 12px;
    }

    .custom-table tbody td {
        padding: 12px 14px;
        font-size: 13px;
        color: #1e293b;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }
    .custom-table tbody tr:hover td {
        background-color: #fff9f8;
    }

    /* Badges */
    .badge-status {
        border-radius: 50px !important;
        padding: 5px 14px !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        display: inline-block;
        text-transform: capitalize;
    }
    .badge-paid {
        background-color: #cfece0 !important;
        color: #15803d !important;
    }
    .badge-partially {
        background-color: #fef3c7 !important;
        color: #b45309 !important;
    }
    .badge-pending {
        background-color: #fee2e2 !important;
        color: #dc2626 !important;
    }

    /* 3-Dots Action Dropdown (Floating & Never Clipped) */
    .action-dropdown-wrap {
        position: relative !important;
        display: inline-block !important;
    }
    .action-menu-toggle {
        background: #ffffff;
        border: 1.5px solid #d7dde8;
        border-radius: 50% !important;
        color: #1e293b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 0;
        outline: none;
    }
    .action-menu-toggle:hover, .action-dropdown-wrap.active .action-menu-toggle {
        background: #fff5f2;
        border-color: #f89884;
        color: #f89884;
    }

    .action-menu-box {
        display: none;
        position: fixed !important;
        background: #ffffff !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 14px !important;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15) !important;
        padding: 6px 0 !important;
        min-width: 175px !important;
        z-index: 999999 !important;
    }

    .action-menu-box.show {
        display: block !important;
    }
    .action-menu-box .dropdown-item {
        font-size: 13px;
        color: #334155;
        padding: 8px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.15s ease;
        background: transparent;
        border: none;
        width: 100%;
        text-align: left;
        text-decoration: none;
    }
    .action-menu-box .dropdown-item:hover {
        background: #fff5f2;
        color: #f89884;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 22px !important;
        border: none !important;
        box-shadow: 0 15px 35px rgba(0,0,0,0.12);
        overflow: hidden;
    }
    .modal-header {
        background-color: #f89884;
        color: #ffffff;
        padding: 16px 24px;
        border-bottom: none;
    }
    .modal-title {
        font-weight: 700;
        font-size: 17px;
        color: #ffffff;
    }
    .modal-header .close {
        color: #ffffff !important;
        opacity: 0.9 !important;
        font-size: 26px !important;
        font-weight: 300 !important;
        text-shadow: none !important;
        outline: none !important;
        border: none !important;
        background: transparent !important;
        padding: 0 !important;
        margin: -4px 0 0 auto !important;
        line-height: 1 !important;
        cursor: pointer !important;
        transition: transform 0.15s ease, opacity 0.15s ease;
    }
    .modal-header .close:hover {
        opacity: 1 !important;
        transform: scale(1.15);
    }
    .modal-header .close span {
        font-size: 28px;
        line-height: 1;
        display: block;
    }
    #paymentInfoModal .table td {
        padding: 11px 18px !important;
        vertical-align: middle;
    }
    .modal-body {
        padding: 24px 28px;
    }
    .modal-footer {
        border-top: 1px solid #f1f5f9;
        padding: 16px 24px;
    }
</style>

<div class="page-wrapper">
    <div class="content px-3 px-md-4 pt-3 pb-4">

        <!-- Card Header Matching Appointment / Followup / Invoice -->
        <div class="card border-0 mb-4 table-container-card overflow-hidden">
            <div class="card-header">
                <h3 class="card-title d-inline-block text-white mb-0" style="font-weight: 700; font-size: 19px;">
                    <i class="fa fa-shopping-cart px-2"></i> Purchase Bills
                </h3>

                <div class="d-flex align-items-center gap-2 flex-wrap" style="gap:10px">
                    <a href="{{ route('purchase.ai-bill') }}" class="btn btn-hdr">
                        <i class="fas fa-magic"></i> AI Bill Upload
                    </a>
                    <!-- <a href="{{ route('purchase.export') }}" class="btn btn-hdr">
                        <i class="fas fa-download"></i> Export Excel
                    </a>
                    <a href="{{ route('purchase.export_pdf') }}" class="btn btn-hdr">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a> -->
                    <a href="{{ route('purchase.add') }}" class="btn btn-hdr" >
                        <i class="fa fa-plus"></i> Add Purchase
                    </a>
                </div>
            </div>

            <div class="card-body p-3 p-md-4">
                <!-- KPI Summary Row -->
                <div class="d-flex flex-wrap gap-3 mb-4" style="gap:10px">
                    <div class="kpi-pill kpi-pill-pending">
                        <span>Total Pending:</span>
                        <span id="kpiTotalPending">₹{{ number_format($totalPending ?? 0, 2) }}</span>
                    </div>
                    <div class="kpi-pill kpi-pill-green">
                        <span>Total Paid:</span>
                        <span id="kpiTotalPaid">₹{{ number_format($totalPaid ?? 0, 2) }}</span>
                    </div>
                    <div class="kpi-pill kpi-pill-coral">
                        <span>Total Purchase:</span>
                        <span id="kpiTotalPurchases">₹{{ number_format($totalPurchases ?? 0, 2) }}</span>
                    </div>
                </div>

                <!-- Filters Bar -->
                <div class="p-3 mb-4 rounded-4" style="background: #f8fafc; border: 1.5px solid #e2e8f0;">
                    <div class="row g-2 align-items-center">
                        <!-- Search -->
                        <div class="col-xl-3 col-lg-3 col-md-6 col-12">
                            <div class="position-relative">
                                <input type="text" id="filterSearch" class="form-control" placeholder="Search bill no, vendor..." style="padding-left: 36px !important;">
                                <i class="fas fa-search position-absolute" style="left: 14px; top: 12px; color: #94a3b8; font-size: 13px;"></i>
                            </div>
                        </div>

                        <!-- Vendor Filter (Select2) -->
                        <div class="col-xl-2 col-lg-2 col-md-6 col-12">
                            <select id="filterVendor" class="form-control select2">
                                <option value="">All Vendors</option>
                                @foreach($vendors as $ven)
                                    <option value="{{ $ven->id }}">{{ $ven->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Filter (Select2) -->
                        <div class="col-xl-2 col-lg-2 col-md-4 col-6">
                            <select id="filterMonth" class="form-control select2">
                                <option value="">All Months</option>
                                <option value="01">January</option>
                                <option value="02">February</option>
                                <option value="03">March</option>
                                <option value="04">April</option>
                                <option value="05">May</option>
                                <option value="06">June</option>
                                <option value="07">July</option>
                                <option value="08">August</option>
                                <option value="09">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>

                        <!-- Year Filter (Select2) -->
                        <div class="col-xl-2 col-lg-2 col-md-4 col-6">
                            <select id="filterYear" class="form-control select2">
                                <option value="">All Years</option>
                                @foreach($years as $yr)
                                    <option value="{{ $yr }}" {{ $yr == date('Y') ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter (Select2) -->
                        <div class="col-xl-2 col-lg-2 col-md-4 col-6">
                            <select id="filterStatus" class="form-control select2">
                                <option value="">All Status</option>
                                <option value="completed">Completed</option>
                                <option value="partially">Partially</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>

                        <!-- Show Per Page -->
                        <div class="col-xl-1 col-lg-1 col-md-4 col-6">
                            <select id="filterPerPage" class="form-control select2 text-center" title="Show per page">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Main Purchases Table -->
                <div class="table-responsive">
                    <table class="table custom-table mb-0" id="purchaseTable">
                        <thead>
                            <tr>
                                <th>Bill Number</th>
                                <th>Purchase Date</th>
                                <th>Vendor Name</th>
                                <th>Due Date</th>
                                <th>Interest Amount</th>
                                <th>Grand Total</th>
                                <th>Final Payable</th>
                                <th>Payment Status</th>
                                <th class="text-center" style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="purchaseTableBody">
                            @forelse($purchases as $p)
                                @php
                                    $pDate = $p->purchase_date ? date('d-M-Y', strtotime($p->purchase_date)) : ($p->created_at ? $p->created_at->format('d-M-Y') : '-');
                                    $dueDate = $p->due_date ? date('d-M-Y', strtotime($p->due_date)) : '-';
                                    $isOverdue = $p->due_date && (strtotime($p->due_date) < strtotime(date('Y-m-d'))) && (float)$p->remaining_amount > 0;
                                    $status = strtolower($p->status ?? 'pending');
                                    if ($status === 'paid') $status = 'completed';
                                    $interestAmt = (float)($p->interest_amount ?? 0);
                                    $grandTotal = (float)($p->grand_total ?? 0);
                                    $finalPayable = (float)($p->final_amount_after_interest ?? ($grandTotal + $interestAmt));
                                    $remaining = (float)($p->remaining_amount ?? 0);
                                    $vendorName = optional($p->vendor)->name ?? ($p->vendor_id ?: 'Unknown Vendor');
                                    $medNames = $p->purchases ? $p->purchases->map(fn($item) => optional($item->medicine)->name ?? ($item->item_name ?? ''))->filter()->implode(' ') : '';
                                    $batchNos = $p->purchases ? $p->purchases->map(fn($item) => $item->batch_no ?? optional($item->medicine)->batch_no)->filter()->implode(' ') : '';
                                @endphp
                                <tr class="purchase-row"
                                    data-id="{{ $p->id }}"
                                    data-bill="{{ $p->bill_no ?? $p->invoice_number }}"
                                    data-vendor-id="{{ $p->vendor_id }}"
                                    data-vendor-name="{{ strtolower($vendorName) }}"
                                    data-medicines="{{ strtolower($medNames) }}"
                                    data-batches="{{ strtolower($batchNos) }}"
                                    data-month="{{ $p->purchase_date ? date('m', strtotime($p->purchase_date)) : ($p->created_at ? $p->created_at->format('m') : '') }}"
                                    data-year="{{ $p->purchase_date ? date('Y', strtotime($p->purchase_date)) : ($p->created_at ? $p->created_at->format('Y') : '') }}"
                                    data-status="{{ $status }}"
                                    data-grand-total="{{ $grandTotal }}"
                                    data-remaining="{{ $remaining }}"
                                    data-paid="{{ (float)($p->paid ?? 0) }}">
                                    <td>
                                        <a href="{{ route('purchase.invoice', $p->id) }}" style="font-weight: 700; color: #1e293b; text-decoration: none;">
                                            {{ $p->bill_no ?? $p->invoice_number ?? ('PINV-' . str_pad($p->id, 5, '0', STR_PAD_LEFT)) }}
                                        </a>
                                    </td>
                                    <td>{{ $pDate }}</td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $vendorName }}</span>
                                    </td>
                                    
                                    <td>
                                        @if($p->due_date)
                                            <span class="{{ $isOverdue ? 'text-danger fw-bold' : 'text-success fw-semibold' }}">{{ $dueDate }}</span>
                                            @if($isOverdue)
                                                <span class="badge bg-danger ms-1" style="border-radius: 50px; font-size: 10px; padding: 2px 8px;">Overdue</span>
                                            @endif
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                    <td>{{ $interestAmt != 0 ? ('₹' . number_format($interestAmt, 2)) : '-' }}</td>
                                    <td class="fw-bold">₹{{ number_format($grandTotal, 2) }}</td>
                                    <td class="fw-bold text-success">₹{{ number_format($finalPayable, 2) }}</td>
                                    <td>
                                        @if($remaining < 0)
                                            <span class="badge-status" style="background-color: #fee2e2; color: #dc2626;">Extra Paid: ₹{{ number_format(abs($remaining), 2) }}</span>
                                        @elseif($remaining <= 0 || $status === 'completed')
                                            <span class="badge-status badge-paid">Completed</span>
                                        @elseif($status === 'partially' || ($p->paid > 0 && $remaining > 0))
                                            <span class="badge-status badge-partially">Partially</span>
                                        @else
                                            <span class="badge-status badge-pending">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="action-dropdown-wrap">
                                            <button type="button" class="action-menu-toggle" title="Actions">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="action-menu-box">
                                                @if($remaining > 0)
                                                    <a href="javascript:void(0);" class="dropdown-item make-payment-btn"
                                                       data-id="{{ $p->id }}"
                                                       data-bill="{{ $p->bill_no ?? $p->invoice_number }}"
                                                       data-grand-total="{{ $grandTotal }}"
                                                       data-remaining="{{ $remaining }}">
                                                        <i class="fas fa-rupee-sign" style="color: #f89884; width: 16px;"></i>
                                                        <span style="color: #f89884; font-weight: 700;">Make Payment</span>
                                                    </a>
                                                @endif
                                                <a href="javascript:void(0);" class="dropdown-item view-info-btn"
                                                   data-id="{{ $p->id }}"
                                                   data-grand-total="{{ number_format($grandTotal, 2) }}"
                                                   data-remaining="{{ number_format($remaining, 2) }}"
                                                   data-due-days="{{ $p->payment_due_days ?? '-' }}"
                                                   data-due-date="{{ $dueDate }}"
                                                   data-interest-before="{{ $p->interest_type ?? '0.00' }}"
                                                   data-interest-after="{{ $p->interest_amount ?? '0.00' }}"
                                                   data-interest-amount="₹{{ number_format($interestAmt, 2) }}"
                                                   data-final-payable="₹{{ number_format($finalPayable, 2) }}"
                                                   data-completed-date="{{ $remaining <= 0 ? ($p->updated_at ? $p->updated_at->format('d-M-Y') : '-') : 'N/A' }}">
                                                    <i class="fas fa-info-circle text-muted" style="width: 16px;"></i>
                                                    <span>Payment Info</span>
                                                </a>
                                                <a href="javascript:void(0);" class="dropdown-item view-history-btn" data-id="{{ $p->id }}" data-bill="{{ $p->bill_no ?? $p->invoice_number }}">
                                                    <i class="fas fa-history text-muted" style="width: 16px;"></i>
                                                    <span>History</span>
                                                </a>
                                                <a href="{{ route('purchase.details', $p->id) }}" class="dropdown-item">
                                                    <i class="fas fa-eye text-muted" style="width: 16px;"></i>
                                                    <span>View</span>
                                                </a>
                                                <a href="{{ route('purchase.edit', $p->id) }}" class="dropdown-item">
                                                    <i class="fas fa-edit text-muted" style="width: 16px;"></i>
                                                    <span>Edit</span>
                                                </a>
                                                <a href="{{ route('purchase.invoice', $p->id) }}" class="dropdown-item">
                                                    <i class="fas fa-file-invoice text-muted" style="width: 16px;"></i>
                                                    <span>Invoice</span>
                                                </a>
                                                <a href="{{ route('purchase.invoice', $p->id) }}?download_pdf=1" target="_blank" class="dropdown-item">
                                                    <i class="fas fa-file-pdf text-muted" style="width: 16px;"></i>
                                                    <span>Download PDF</span>
                                                </a>
                                                <div class="dropdown-divider my-1" style="border-top: 1px solid #f1f5f9;"></div>
                                                <a href="javascript:void(0);" class="dropdown-item text-danger delete-purchase-btn" data-id="{{ $p->id }}">
                                                    <i class="fas fa-trash-alt text-danger" style="width: 16px;"></i>
                                                    <span class="text-danger fw-semibold">Delete</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="noDataRow">
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fa-3x mb-2 d-block" style="color: #cbd5e1;"></i>
                                        No purchase records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer count -->
                <div class="d-flex justify-content-between align-items-center pt-3 border-top flex-wrap gap-2" style="font-size: 13px;">
                    <span class="text-muted" id="showingCountText">Showing {{ count($purchases) }} entries</span>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- 1. Make Payment Modal -->
<div class="modal fade" id="makePaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="makePaymentForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-rupee-sign me-1"></i> Make Payment - <span id="mpBillNo"></span></h5>
                    <button type="button" class="btn-close-custom" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" title="Close"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="purchase_id" id="mpPurchaseId">

                    <!-- Top Bar: History expander toggle -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-semibold" style="font-size: 13px;">Remaining Due: <strong class="text-danger" id="mpRemainingDisplay">₹0.00</strong></span>
                        <button type="button" class="btn btn-sm pill-btn btn-clinic-secondary" id="btnToggleInModalHistory" style="padding: 3px 12px; font-size: 12px;">
                            <i class="fas fa-history"></i> View History
                        </button>
                    </div>

                    <!-- In-Modal History Box (collapsible) -->
                    <div id="mpHistoryBox" class="p-2 mb-3 rounded-3 d-none" style="background: #f8fafc; border: 1px solid #e2e8f0; max-height: 150px; overflow-y: auto; font-size: 12px;">
                        <div id="mpHistoryContent" class="text-muted">Loading payments...</div>
                    </div>

                    <!-- Payment Mode -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 13px;">Payment Method</label>
                        <select class="form-control select2" name="payment_method" id="mpPaymentMethod" required>
                            <option value="cash">Cash</option>
                            <option value="online">Online / UPI</option>
                            <option value="cash_online">Cash + Online</option>
                        </select>
                    </div>

                    <!-- Paid Type -->
                    <div class="mb-3" id="mpPaidTypeDiv">
                        <label class="form-label fw-semibold" style="font-size: 13px;">Paid Type</label>
                        <select class="form-control select2" name="paid_type" id="mpPaidType">
                            <option value="full">Fully Paid</option>
                            <option value="partial">Partially Paid</option>
                        </select>
                    </div>

                    <!-- Single Amount Row (for Cash or Online) -->
                    <div class="row g-2 mb-3" id="mpSingleAmountDiv">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Enter Amount</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="amount" id="mpEnterAmount">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Pending Amount</label>
                            <input type="text" class="form-control" id="mpPendingAmount" readonly style="background-color: #f1f5f9;">
                        </div>
                    </div>

                    <!-- Cash + Online Amount Row -->
                    <div class="row g-2 mb-3 d-none" id="mpCashOnlineDiv">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Cash Amount</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="cash_amount" id="mpCashAmount" value="0.00">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Online Amount</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="online_amount" id="mpOnlineAmount" value="0.00">
                        </div>
                        <div class="col-12 mt-2">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Pending Balance</label>
                            <input type="text" class="form-control" id="mpCashOnlinePending" readonly style="background-color: #f1f5f9;">
                        </div>
                    </div>

                    <!-- Bank Selection (for Online or Cash+Online) -->
                    <div class="mb-3 d-none" id="mpBankDiv">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label mb-0 fw-semibold" style="font-size: 13px;">Select Bank</label>
                            <button type="button" class="btn btn-sm pill-btn" style="background-color: #f89884; color: #fff; padding: 2px 10px; font-size: 11px;" data-bs-toggle="modal" data-bs-target="#addBankModal">+ Add Bank</button>
                        </div>
                        <select name="bank_id" id="mpBankId" class="form-control select2">
                            <option value="">Select Bank</option>
                            @foreach($banks as $b)
                                <option value="{{ $b->id }}">{{ $b->bank_name ?? $b->name }} ({{ $b->account_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Date & Remarks -->
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Payment Date</label>
                            <input type="date" class="form-control" name="payment_date" id="mpPaymentDate" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Remarks</label>
                            <input type="text" class="form-control" name="remarks" id="mpRemarks" placeholder="Optional notes">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn pill-btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 50px !important;">Cancel</button>
                    <button type="submit" class="btn pill-btn" id="btnSubmitPayment" style="background-color: #f89884; color: #fff;">
                        <i class="fas fa-check me-1"></i> Submit Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. Payment & Interest Info Modal -->
<div class="modal fade" id="paymentInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle me-1"></i> Payment & Interest Details</h5>
                <button type="button" class="btn-close-custom" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" title="Close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body p-3">
                <div class="table-responsive rounded" style="border: 1.5px solid #e2e8f0; overflow: hidden; min-height: auto !important; padding-bottom: 0 !important;">
                    <table class="table table-bordered mb-0" style="font-size: 13.5px;">
                        <tbody>
                            <tr>
                                <td class="text-muted fw-semibold">Grand Total</td>
                                <td class="fw-bold" id="infoGrandTotal">₹0.00</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Remaining Due</td>
                                <td class="fw-bold text-danger" id="infoRemaining">₹0.00</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Payment Due Days</td>
                                <td id="infoDueDays">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Due Date</td>
                                <td id="infoDueDate">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Completed Date</td>
                                <td id="infoCompletedDate">N/A</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Interest Rate (Before Due)</td>
                                <td id="infoInterestBefore">0%</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Interest Rate (After Due)</td>
                                <td id="infoInterestAfter">0%</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Interest Amount</td>
                                <td id="infoInterestAmount">₹0.00</td>
                            </tr>
                            <tr style="background: #fff5f2;">
                                <td class="fw-bold text-dark">Final Payable Amount</td>
                                <td class="fw-bold" style="color: #f89884; font-size: 15px;" id="infoFinalPayable">₹0.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn pill-btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- 3. Global Payment History Modal -->
<div class="modal fade" id="paymentHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-history me-1"></i> Payment History - <span id="phBillNo"></span></h5>
                <button type="button" class="btn-close-custom" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" title="Close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" style="font-size: 13px;">
                        <thead style="background: #f8fafc;">
                            <tr>
                                <th>#</th>
                                <th>Payment Date</th>
                                <th>Method</th>
                                <th class="text-end">Paid Amount</th>
                                <th class="text-end">Remaining</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="phTableBody">
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">Loading payment records...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn pill-btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- 4. Quick Add Bank Modal -->
<div class="modal fade" id="addBankModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="quickAddBankForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-university me-1"></i> Quick Add Bank Account</h5>
                    <button type="button" class="btn-close-custom" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" title="Close"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 13px;">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="bank_name" id="newBankName" placeholder="e.g. HDFC Bank" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 13px;">Account Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="account_number" id="newBankAccountNo" placeholder="Account Number" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 13px;">IFSC Code</label>
                            <input type="text" class="form-control" name="ifsc_code" id="newBankIfsc" placeholder="IFSC Code">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Branch Name</label>
                            <input type="text" class="form-control" name="branch_name" id="newBankBranch" placeholder="Branch Name">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn pill-btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn pill-btn" id="btnSaveNewBank" style="background-color: #f89884; color: #fff;">Save Bank</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {

    // Initialize Select2 on filters and modals
    $('.select2').select2({ width: '100%' });

    // Explicit Modal Dismiss handler (ensures BS4/BS5 close & cancel work smoothly)
    $(document).on('click', '[data-dismiss="modal"], [data-bs-dismiss="modal"], .modal .close', function(e) {
        $(this).closest('.modal').modal('hide');
    });

    // -------------------------------------------------------------
    // 3-Dots Action Dropdown Handling (Opens Downwards)
    // -------------------------------------------------------------
    $(document).on('click', '.action-dropdown-wrap, .action-menu-toggle', function(e) {
        if ($(e.target).closest('.action-menu-box').length) {
            return;
        }
        e.preventDefault();
        e.stopPropagation();
        var $btn = $(this).is('.action-menu-toggle') ? $(this) : $(this).find('.action-menu-toggle');
        var $wrap = $(this).closest('.action-dropdown-wrap');
        var $menu = $wrap.find('.action-menu-box');
        var wasVisible = $menu.hasClass('show');

        $('.action-menu-box').removeClass('show');
        $('.action-dropdown-wrap').removeClass('active');

        if (!wasVisible) {
            $wrap.addClass('active');
            $menu.addClass('show');
            var rect = $btn[0].getBoundingClientRect();
            var menuWidth = $menu.outerWidth() || 175;
            var menuHeight = $menu.outerHeight() || 220;
            var windowHeight = $(window).height();
            var topPos = rect.bottom + 4;
            if (topPos + menuHeight > windowHeight && rect.top > menuHeight) {
                topPos = rect.top - menuHeight - 4;
            }
            var leftPos = rect.right - menuWidth;
            if (leftPos < 10) leftPos = 10;
            $menu.css({
                'position': 'fixed',
                'top': topPos + 'px',
                'left': leftPos + 'px',
                'right': 'auto',
                'bottom': 'auto'
            });
        }
    });

    $(window).on('scroll resize', function() {
        $('.action-menu-box').removeClass('show');
        $('.action-dropdown-wrap').removeClass('active');
    });
    $('.table-responsive').on('scroll', function() {
        $('.action-menu-box').removeClass('show');
        $('.action-dropdown-wrap').removeClass('active');
    });

    // Close action dropdown menu when clicking any item inside it
    $(document).on('click', '.action-menu-box .dropdown-item, .action-menu-box a, .action-menu-box button', function() {
        $('.action-menu-box').removeClass('show');
        $('.action-dropdown-wrap').removeClass('active');
    });

    // When ANY modal opens, close all action dropdown menus immediately
    $(document).on('show.bs.modal', '.modal', function() {
        $('.action-menu-box').removeClass('show');
        $('.action-dropdown-wrap').removeClass('active');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.action-dropdown-wrap').length) {
            $('.action-menu-box').removeClass('show');
            $('.action-dropdown-wrap').removeClass('active');
        }
    });

    // -------------------------------------------------------------
    // Filtering Logic (Search, Month, Year, Vendor, Status, PerPage)
    // -------------------------------------------------------------
    function filterRows() {
        var search = ($('#filterSearch').val() || '').toLowerCase().trim();
        var vendorId = $('#filterVendor').val();
        var month = $('#filterMonth').val();
        var year = $('#filterYear').val();
        var status = ($('#filterStatus').val() || '').toLowerCase();
        var perPage = parseInt($('#filterPerPage').val()) || 10;

        var visibleCount = 0;
        var sumTotal = 0;
        var sumPaid = 0;
        var sumPending = 0;

        $('.purchase-row').each(function() {
            var $row = $(this);
            var rBill = ($row.data('bill') || '').toString().toLowerCase();
            var rVendor = ($row.data('vendor-name') || '').toString().toLowerCase();
            var rVendorId = ($row.data('vendor-id') || '').toString();
            var rMonth = ($row.data('month') || '').toString();
            var rYear = ($row.data('year') || '').toString();
            var rStatus = ($row.data('status') || '').toString().toLowerCase();

            var rGrand = parseFloat($row.data('grand-total')) || 0;
            var rPaid = parseFloat($row.data('paid')) || 0;
            var rRemaining = parseFloat($row.data('remaining')) || 0;

            var rMeds = ($row.data('medicines') || '').toString().toLowerCase();
            var rBatches = ($row.data('batches') || '').toString().toLowerCase();
            var matchSearch = !search || rBill.includes(search) || rVendor.includes(search) || rMeds.includes(search) || rBatches.includes(search);
            var matchVendor = !vendorId || rVendorId === vendorId;
            var matchMonth = !month || rMonth === month;
            var matchYear = !year || rYear === year;
            var matchStatus = !status || rStatus === status;

            if (matchSearch && matchVendor && matchMonth && matchYear && matchStatus) {
                visibleCount++;
                sumTotal += rGrand;
                sumPaid += rPaid;
                sumPending += rRemaining;

                if (visibleCount <= perPage) {
                    $row.show();
                } else {
                    $row.hide();
                }
            } else {
                $row.hide();
            }
        });

        // Update KPIs to match filtered view
        $('#kpiTotalPurchases').text('₹' + sumTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#kpiTotalPaid').text('₹' + sumPaid.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#kpiTotalPending').text('₹' + sumPending.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        $('#showingCountText').text('Showing ' + Math.min(visibleCount, perPage) + ' of ' + visibleCount + ' entries');

        if (visibleCount === 0) {
            if (!$('#tempNoDataRow').length) {
                $('#purchaseTableBody').append('<tr id="tempNoDataRow"><td colspan="9" class="text-center py-4 text-muted">No matching purchase records found.</td></tr>');
            }
        } else {
            $('#tempNoDataRow').remove();
        }
    }

    $('#filterSearch').on('keyup', filterRows);
    $('#filterVendor, #filterMonth, #filterYear, #filterStatus, #filterPerPage').on('change', filterRows);

    // -------------------------------------------------------------
    // Payment & Interest Info Modal
    // -------------------------------------------------------------
    $(document).on('click', '.view-info-btn', function() {
        var $btn = $(this);
        $('#infoGrandTotal').text('₹' + $btn.data('grand-total'));
        $('#infoRemaining').text('₹' + $btn.data('remaining'));
        $('#infoDueDays').text($btn.data('due-days'));
        $('#infoDueDate').text($btn.data('due-date'));
        $('#infoCompletedDate').text($btn.data('completed-date'));
        $('#infoInterestBefore').text(($btn.data('interest-before') || '0.00') + '%');
        $('#infoInterestAfter').text(($btn.data('interest-after') || '0.00') + '%');
        $('#infoInterestAmount').text($btn.data('interest-amount'));
        $('#infoFinalPayable').text($btn.data('final-payable'));

        $('#paymentInfoModal').modal('show');
    });

    // -------------------------------------------------------------
    // Payment History Modal
    // -------------------------------------------------------------
    function loadPaymentHistory(purchaseId, targetTbody) {
        targetTbody.html('<tr><td colspan="6" class="text-center py-3 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Loading payments...</td></tr>');
        $.ajax({
            url: '/api/purchase/payment-history/' + purchaseId,
            type: 'GET',
            success: function(res) {
                if (res.status && Array.isArray(res.payments) && res.payments.length > 0) {
                    var html = '';
                    res.payments.forEach(function(pay, idx) {
                        var pDate = pay.payment_date || (pay.created_at ? pay.created_at.substring(0, 10) : '-');
                        var method = (pay.payment_method || 'Cash').toUpperCase();
                        var amt = parseFloat(pay.payment_amount || 0).toFixed(2);
                        var rem = parseFloat(pay.remaining_amount || 0).toFixed(2);
                        var note = pay.remarks || '-';

                        html += '<tr>' +
                            '<td>' + (idx + 1) + '</td>' +
                            '<td>' + pDate + '</td>' +
                            '<td><span class="badge bg-light text-dark" style="border-radius: 50px;">' + method + '</span></td>' +
                            '<td class="text-end fw-bold text-success">₹' + amt + '</td>' +
                            '<td class="text-end text-danger">₹' + rem + '</td>' +
                            '<td>' + note + '</td>' +
                        '</tr>';
                    });
                    targetTbody.html(html);
                } else {
                    targetTbody.html('<tr><td colspan="6" class="text-center py-3 text-muted">No payments recorded yet.</td></tr>');
                }
            },
            error: function() {
                targetTbody.html('<tr><td colspan="6" class="text-center py-3 text-danger">Failed to fetch payment history.</td></tr>');
            }
        });
    }

    $(document).on('click', '.view-history-btn', function() {
        var id = $(this).data('id');
        var bill = $(this).data('bill') || ('#' + id);
        $('#phBillNo').text(bill);
        $('#paymentHistoryModal').modal('show');
        loadPaymentHistory(id, $('#phTableBody'));
    });

    // -------------------------------------------------------------
    // Make Payment Modal Handling
    // -------------------------------------------------------------
    var currentMakePaymentRemaining = 0;

    $(document).on('click', '.make-payment-btn', function() {
        var id = $(this).data('id');
        var bill = $(this).data('bill') || ('#' + id);
        var remaining = parseFloat($(this).data('remaining')) || 0;

        currentMakePaymentRemaining = remaining;

        $('#mpPurchaseId').val(id);
        $('#mpBillNo').text(bill);
        $('#mpRemainingDisplay').text('₹' + remaining.toFixed(2));
        $('#mpEnterAmount').val(remaining.toFixed(2));
        $('#mpPendingAmount').val('0.00');

        $('#mpPaymentMethod').val('cash').trigger('change');
        $('#mpPaidType').val('full').trigger('change');
        $('#mpRemarks').val('');
        $('#mpHistoryBox').addClass('d-none');

        $('#makePaymentModal').modal('show');
    });

    // In-modal history toggle
    $('#btnToggleInModalHistory').on('click', function() {
        var $box = $('#mpHistoryBox');
        if ($box.hasClass('d-none')) {
            $box.removeClass('d-none');
            var pId = $('#mpPurchaseId').val();
            $box.html('<div class="text-center py-2"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
            $.ajax({
                url: '/api/purchase/payment-history/' + pId,
                type: 'GET',
                success: function(res) {
                    if (res.status && Array.isArray(res.payments) && res.payments.length > 0) {
                        var h = '<ul class="list-unstyled mb-0">';
                        res.payments.forEach(function(p) {
                            h += '<li class="d-flex justify-content-between py-1 border-bottom">' +
                                 '<span>' + (p.payment_date || '-') + ' (' + (p.payment_method || 'Cash') + ')</span>' +
                                 '<span class="fw-bold text-success">₹' + parseFloat(p.payment_amount).toFixed(2) + '</span>' +
                                 '</li>';
                        });
                        h += '</ul>';
                        $box.html(h);
                    } else {
                        $box.html('<div class="text-center text-muted py-1">No payment history yet.</div>');
                    }
                }
            });
        } else {
            $box.addClass('d-none');
        }
    });

    // Toggle Payment Method in Make Payment
    $('#mpPaymentMethod').on('change', function() {
        var method = $(this).val();
        if (method === 'cash_online') {
            $('#mpSingleAmountDiv').addClass('d-none');
            $('#mpCashOnlineDiv').removeClass('d-none');
            $('#mpBankDiv').removeClass('d-none');
            recalcCashOnline();
        } else if (method === 'online') {
            $('#mpSingleAmountDiv').removeClass('d-none');
            $('#mpCashOnlineDiv').addClass('d-none');
            $('#mpBankDiv').removeClass('d-none');
            recalcSingleAmount();
        } else {
            $('#mpSingleAmountDiv').removeClass('d-none');
            $('#mpCashOnlineDiv').addClass('d-none');
            $('#mpBankDiv').addClass('d-none');
            recalcSingleAmount();
        }
    });

    $('#mpPaidType').on('change', function() {
        if ($(this).val() === 'full') {
            $('#mpEnterAmount').val(currentMakePaymentRemaining.toFixed(2));
            $('#mpPendingAmount').val('0.00');
        }
        recalcSingleAmount();
    });

    $('#mpEnterAmount').on('input', recalcSingleAmount);

    function recalcSingleAmount() {
        var enter = parseFloat($('#mpEnterAmount').val()) || 0;
        var pending = Math.max(0, currentMakePaymentRemaining - enter);
        $('#mpPendingAmount').val(pending.toFixed(2));
    }

    $('#mpCashAmount, #mpOnlineAmount').on('input', recalcCashOnline);

    function recalcCashOnline() {
        var c = parseFloat($('#mpCashAmount').val()) || 0;
        var o = parseFloat($('#mpOnlineAmount').val()) || 0;
        var pending = Math.max(0, currentMakePaymentRemaining - (c + o));
        $('#mpCashOnlinePending').val(pending.toFixed(2));
    }

    // Submit Make Payment
    $('#makePaymentForm').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btnSubmitPayment');
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Processing...').prop('disabled', true);

        $.ajax({
            url: '{{ route("purchase.make-payment") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                $btn.html(originalText).prop('disabled', false);
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Recorded!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', res.message || 'Payment failed', 'error');
                }
            },
            error: function(xhr) {
                $btn.html(originalText).prop('disabled', false);
                Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.message : 'Server error recording payment', 'error');
            }
        });
    });

    // -------------------------------------------------------------
    // Quick Add Bank Handling
    // -------------------------------------------------------------
    $('#quickAddBankForm').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btnSaveNewBank');
        $btn.prop('disabled', true);

        $.ajax({
            url: '{{ route("banks.ajax_store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                $btn.prop('disabled', false);
                if (res.status && res.bank) {
                    var newOpt = new Option(res.bank.bank_name + ' (' + (res.bank.account_number || '') + ')', res.bank.id, true, true);
                    $('#mpBankId').append(newOpt).trigger('change');
                    $('#addBankModal').modal('hide');
                    $('#quickAddBankForm')[0].reset();
                    Swal.fire('Success', 'Bank added successfully!', 'success');
                } else {
                    Swal.fire('Error', res.message || 'Failed to add bank', 'error');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false);
                Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.message : 'Validation failed', 'error');
            }
        });
    });

    // -------------------------------------------------------------
    // Soft Delete Purchase
    // -------------------------------------------------------------
    $(document).on('click', '.delete-purchase-btn', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Delete Purchase Bill?',
            text: 'This will reverse inventory stock and remove this purchase record.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea5455',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/purchase/delete/' + id,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.message,
                                timer: 1200,
                                showConfirmButton: false
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Unable to delete purchase bill.', 'error');
                    }
                });
            }
        });
    });

});
</script>
@endsection
