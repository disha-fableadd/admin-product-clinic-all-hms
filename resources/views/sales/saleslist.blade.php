@extends('layout.app')

@section('title', 'All Sales Bills')

@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@section('content')
<style>
    :root {
        --theme-orange: #f89884;
        --theme-orange-dark: #ea7a63;
        --theme-navy: #002e5b;
        --theme-mint: #cfece0;
        --theme-dark: #1e293b;
    }

    /* GST Tabs (50% Radius Pill & Project Theme Colors) */
    .gst-tabs {
        display: inline-flex;
        background: #fff5f2;
        padding: 4px;
        border-radius: 50px !important;
        margin-bottom: 16px;
        gap: 4px;
        border: 1.5px solid #fed7ce;
    }
    .gst-tab-item {
        padding: 7px 24px;
        font-weight: 700;
        font-size: 13px;
        border-radius: 50px !important;
        cursor: pointer;
        color: #5a6a80;
        transition: all 0.2s ease;
        user-select: none;
    }
    .gst-tab-item:hover:not(.active) {
        color: #f89884;
    }
    .gst-tab-item.active {
        background: #f89884 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(248, 152, 132, 0.4);
    }

    /* Card Header Matching Appointment / Followup / Purchase */
    .card-header {
        background-color: #f89884 !important;
        border-bottom: none !important;
        padding: 16px 24px !important;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .btn-hdr {
        background: #ffffff !important;
        color: #f89884 !important;
        border: none !important;
        font-weight: 700;
        border-radius: 50px !important;
        padding: 8px 18px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        transition: all 0.2s ease;
    }
    .btn-hdr:hover {
        background: #fff5f2 !important;
        color: #e2725b !important;
        transform: translateY(-1px);
    }
    .table-container-card {
        border-radius: 20px !important;
        border: 1.5px solid #e2e8f0;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        background: #ffffff;
        overflow: hidden !important;
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

    /* Main Card */
    .sales-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        background: #ffffff;
        padding: 22px 24px;
        margin-bottom: 30px;
    }

    /* Filter Form Controls (50% Radius Pill) */
    .sales-filter-input, .sales-filter-select {
        height: 38px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 50px !important;
        padding: 6px 18px !important;
        font-size: 13px !important;
        color: #1e293b !important;
        background-color: #ffffff;
        width: 100%;
        outline: none;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .sales-filter-input:focus, .sales-filter-select:focus {
        border-color: #f89884 !important;
        box-shadow: 0 0 0 2px rgba(248, 152, 132, 0.25) !important;
    }

    /* Delete All Button (50% Radius Pill) */
    #deleteAllSalesBtn {
        height: 38px !important;
        border-radius: 50px !important;
        font-size: 13px !important;
        background-color: #f89884 !important;
        border-color: #f89884 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        transition: all 0.2s;
    }
    #deleteAllSalesBtn:hover {
        background-color: #f67e65 !important;
        border-color: #f67e65 !important;
    }

    /* Summary KPI Boxes (50% Radius Pill) */
    .summary-box-pending {
        border: 1.5px solid #ea5455;
        color: #ea5455;
        background-color: #fffaf9;
        font-weight: 700;
        font-size: 13.5px;
        border-radius: 50px !important;
        padding: 8px 18px !important;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .summary-box-paid {
        border: 1.5px solid #28c76f;
        color: #28c76f;
        background-color: #f9fff9;
        font-weight: 700;
        font-size: 13.5px;
        border-radius: 50px !important;
        padding: 8px 18px !important;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .summary-box-total {
        border: 1.5px solid #f89884;
        color: #f89884;
        background-color: #fff8f6;
        font-weight: 700;
        font-size: 13.5px;
        border-radius: 50px !important;
        padding: 8px 18px !important;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    /* Page Spacing Fix */
    .page-wrapper {
        padding-top: 70px !important;
    }
    .page-wrapper > .content,
    .content {
        padding-top: 22px !important;
    }

    /* Show Per Page Dropdown (50% Radius Pill) */
    .per-page-select {
        height: 38px !important;
        border-radius: 50px !important;
        padding: 4px 8px !important;
        font-size: 13px !important;
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        border: 1.5px solid #cbd5e1 !important;
        outline: none;
        background-color: #ffffff;
        text-align: center;
        cursor: pointer;
    }
    .per-page-select:focus {
        border-color: #f89884 !important;
        box-shadow: 0 0 0 3px rgba(248, 152, 132, 0.25) !important;
    }

    /* Table & Status Badges (Coral Header Matching Appointment / Followup / Purchase) */
    .custom-table, .table-sales {
        width: 100% !important;
        min-width: 950px;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }
    .custom-table thead tr th, .table-sales thead tr th, .table-sales th {
        background-color: #f89884 !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 13px;
        padding: 12px 14px;
        border: none;
        white-space: nowrap;
        vertical-align: middle;
    }
    .custom-table thead tr th:first-child, .table-sales thead tr th:first-child {
        border-top-left-radius: 12px;
    }
    .custom-table thead tr th:last-child, .table-sales thead tr th:last-child {
        border-top-right-radius: 12px;
    }
    .custom-table tbody td, .table-sales tbody td, .table-sales td {
        padding: 12px 14px;
        font-size: 13px;
        color: #1e293b;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }
    .custom-table tbody tr:hover td, .table-sales tbody tr:hover td, .table-sales tr:hover td {
        background-color: #fff9f8;
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

    /* Payment Badges (50% Radius Pill) */
    .status-badge {
        display: inline-block;
        padding: 5px 16px;
        border-radius: 50px !important;
        font-weight: 600;
        font-size: 11.5px;
        text-transform: capitalize;
        text-align: center;
        color: #ffffff !important;
        white-space: nowrap;
    }
    .status-badge.type-sales { background-color: #f89884 !important; border-radius: 50px !important; }
    .status-badge.type-challan { background-color: #f89884 !important; border-radius: 50px !important; }

    .status-badge.badge-pending { background-color: #5a6a80 !important; border-radius: 50px !important; }
    .status-badge.badge-cash { background-color: #f89884 !important; border-radius: 50px !important; }
    .status-badge.badge-paid, .status-badge.badge-online { background-color: #28c76f !important; border-radius: 50px !important; }
    .status-badge.badge-debit { background-color: #00cfe8 !important; border-radius: 50px !important; }
    .status-badge.badge-cash_online { background-color: #20c997 !important; border-radius: 50px !important; }
    .status-badge.badge-upi { background-color: #7367f0 !important; border-radius: 50px !important; }

    /* Documentation Button (50% Radius Pill) */
    .btn-doc-orange {
        background-color: #f89884 !important;
        border: 1px solid #f89884 !important;
        color: #ffffff !important;
        border-radius: 50px !important;
        font-size: 12px;
        font-weight: 600;
        padding: 5px 18px !important;
        white-space: nowrap;
        transition: all 0.2s;
    }
    .btn-doc-orange:hover {
        background-color: #f67e65 !important;
        border-color: #f67e65 !important;
        color: #ffffff !important;
    }

    /* Action 3-Dots Dropdown Menu */
    .action-dropdown-wrap {
        position: relative !important;
        display: inline-block !important;
        vertical-align: middle;
    }
    .action-menu-toggle {
        background: #ffffff;
        border: 1.5px solid #d7dde8;
        border-radius: 50px !important;
        color: #1e293b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 32px;
        width: 32px;
        padding: 0;
        cursor: pointer;
        transition: all 0.15s ease;
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
        min-width: 175px !important;
        padding: 6px 0;
        border-radius: 12px !important;
        background: #ffffff !important;
        border: 1px solid #e8ebed;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
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
        font-weight: 500;
        transition: all 0.15s;
        text-decoration: none;
        cursor: pointer;
        background: transparent;
        border: none;
        width: 100%;
        text-align: left;
    }
    .action-menu-box .dropdown-item i {
        font-size: 13px;
        width: 16px;
        text-align: center;
        color: #64748b;
    }
    .action-menu-box .dropdown-item.pay-item {
        color: #f89884;
        font-weight: 700;
    }
    .action-menu-box .dropdown-item.pay-item i {
        color: #f89884;
    }
    .action-menu-box .dropdown-item:hover {
        background-color: #fff5f2;
        color: #f89884;
    }
    .action-menu-box .dropdown-item:hover i {
        color: #f89884;
    }
    .action-menu-box .dropdown-item.text-danger:hover {
        background-color: #fee2e2;
        color: #dc2626;
    }
    .action-menu-box .dropdown-item.text-danger i {
        color: #dc2626;
    }
</style>

<div class="page-wrapper">
    <div class="content px-3 px-md-4 pt-3 pb-4">

        <!-- Card Header Matching Appointment / Followup / Purchase -->
        <div class="card border-0 mb-4 table-container-card overflow-hidden">
            <div class="card-header">
                <h3 class="card-title d-inline-block text-white mb-0" style="font-weight: 700; font-size: 19px;">
                    <i class="fa fa-shopping-bag px-2"></i> Sale Bills
                </h3>

                <div class="d-flex align-items-center gap-2 flex-wrap" style="gap:10px">
                    <a href="{{ route('sales.add') }}" class="btn btn-hdr">
                        <i class="fa fa-plus"></i> New Sale Bill
                    </a>
                </div>
            </div>

            <div class="card-body p-3 p-md-4">
                <!-- Without / With GST Bills Toggle Sub-Tabs -->
                <div class="gst-tabs mb-4">
                    <div class="gst-tab-item" data-gst="without_gst">Without GST Bills</div>
                    <div class="gst-tab-item active" data-gst="with_gst">With GST Bills</div>
                </div>

                <!-- KPI Summary Row Matching Purchase / Appointment -->
                <div class="d-flex flex-wrap gap-3 mb-4" style="gap:10px">
                    <div class="kpi-pill kpi-pill-pending">
                        <span>Total Pending:</span>
                        <span id="summaryPendingAmount">&#8377;0.00</span>
                    </div>
                    <div class="kpi-pill kpi-pill-green">
                        <span>Total Paid:</span>
                        <span id="summaryPaidAmount">&#8377;0.00</span>
                    </div>
                    <div class="kpi-pill kpi-pill-coral">
                        <span>Total Sales:</span>
                        <span id="summaryTotalAmount">&#8377;0.00</span>
                    </div>
                </div>

                <!-- Filter Bar Container -->
                <div class="p-3 mb-4 rounded-4" style="background: #f8fafc; border: 1.5px solid #e2e8f0; overflow: hidden;">
                    <div class="row g-2 align-items-center">
                        <!-- Search sales input -->
                        <div class="col-xl-3 col-lg-3 col-md-6 col-12">
                            <div class="position-relative">
                                <input type="text" id="salesSearchInput" class="sales-filter-input" placeholder="Search sales..." style="padding-left: 36px !important;">
                                <i class="fas fa-search position-absolute" style="left: 14px; top: 12px; color: #94a3b8; font-size: 13px;"></i>
                            </div>
                        </div>

                        <!-- Patient Filter -->
                        <div class="col-xl-2 col-lg-2 col-md-3 col-6">
                            <select id="filterPatient" class="sales-filter-select select2">
                                <option value="">All Patient</option>
                                @foreach($patients as $p)
                                    <option value="{{ $p->id }}">{{ $p->fullname ?? $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Filter -->
                        <div class="col-xl-2 col-lg-2 col-md-3 col-6">
                            <select id="filterMonth" class="sales-filter-select select2">
                                <option value="">All Months</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">
                                        {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div class="col-xl-2 col-lg-2 col-md-3 col-6">
                            <select id="filterYear" class="sales-filter-select select2">
                                <option value="">All Years</option>
                                @foreach($years as $yr)
                                    <option value="{{ $yr }}" {{ $yr == date('Y') ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Delete All Button -->
                        <div class="col-xl-2 col-lg-2 col-md-3 col-6">
                            <button type="button" class="btn w-100 fw-bold" id="deleteAllSalesBtn" style="height: 38px !important; border-radius: 50px !important; background-color: #f89884 !important; border-color: #f89884 !important; color: #ffffff !important; white-space: nowrap; font-size: 12.5px !important; padding: 6px 10px !important;">
                                <i class="fas fa-trash-alt me-1"></i> Delete All
                            </button>
                        </div>

                        <!-- Show Per Page Selector -->
                        <div class="col-xl-1 col-lg-1 col-md-2 col-6">
                            <select id="perPageSelect" class="per-page-select text-center" title="Show per page">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Orders Table Container with Coral Header -->
                <div class="table-responsive">
                    <table class="table custom-table table-sales mb-0" id="salesTable">
                        <thead>
                            <tr>
                                <th style="width: 36px; text-align: center;">
                                    <input type="checkbox" id="selectAllCheckbox" style="cursor: pointer; width: 16px; height: 16px;">
                                </th>
                                <th>Bill No</th>
                                <th>Bill Date</th>
                                <th>Patient Name</th>
                                <th>Mobile Number</th>
                                <th>Payment Mode</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Documentations</th>
                                <th style="text-align: center; width: 60px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="salesTableBody">
                            <!-- Populated via JavaScript dynamically -->
                        </tbody>
                    </table>
                </div>

                <!-- Footer Pagination & Count -->
                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top flex-wrap gap-2" id="paginationWrap">
                    <div id="tableInfoText" style="font-size: 13px; color: #64748b; font-weight: 500;">
                        Showing 0 items
                    </div>
                    <ul class="pagination pagination-sm mb-0" id="paginationList"></ul>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ================= MODAL: Product Documentation ================= -->
<div class="modal fade" id="documentationModal" tabindex="-1" aria-labelledby="documentationLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 22px !important; overflow: hidden; border: none !important; box-shadow: 0 15px 35px rgba(0,0,0,0.12);">
            <div class="modal-header d-flex justify-content-between align-items-center" style="background-color: #f89884; color: #ffffff; padding: 16px 24px; border-bottom: none;">
                <h5 class="modal-title font-weight-bold" id="documentationLabel" style="color: #ffffff !important; font-size: 17px; margin: 0; font-weight: 700;"><i class="fas fa-file-alt me-2"></i> Product Documentation</h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="color: #ffffff !important; opacity: 0.95 !important; font-size: 26px !important; font-weight: 300 !important; text-shadow: none !important; border: none !important; background: transparent !important; padding: 0 !important; cursor: pointer; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="documentationForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="docOrderId" name="order_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Batch Number -->
                        <div class="col-md-6 col-12">
                            <label class="form-label" style="font-weight: 600; font-size: 13px; color: #334155;">Batch Number</label>
                            <input type="text" class="form-control" name="batch_number" id="doc_batch_number" placeholder="Enter Batch Number" style="border-radius: 50px !important; height: 42px; border: 1.5px solid #cbd5e1; padding: 6px 18px;">
                        </div>

                        <!-- Manufacturing Date -->
                        <div class="col-md-6 col-12">
                            <label class="form-label" style="font-weight: 600; font-size: 13px; color: #334155;">Manufacturing Date</label>
                            <input type="month" class="form-control" name="manufacturing_date" id="doc_mfg_date" style="border-radius: 50px !important; height: 42px; border: 1.5px solid #cbd5e1; padding: 6px 18px;">
                        </div>

                        <!-- Expiry Date -->
                        <div class="col-md-6 col-12">
                            <label class="form-label" style="font-weight: 600; font-size: 13px; color: #334155;">Expiry Date</label>
                            <input type="month" class="form-control" name="expiry_date" id="doc_expiry_date" style="border-radius: 50px !important; height: 42px; border: 1.5px solid #cbd5e1; padding: 6px 18px;">
                        </div>

                        <!-- Upload COA (PDF) -->
                        <div class="col-md-6 col-12">
                            <label class="form-label" style="font-weight: 600; font-size: 13px; color: #334155;">Upload COA (PDF)</label>
                            <input type="file" class="form-control" name="coa_file[]" id="doc_coa_file" accept="application/pdf" multiple style="border-radius: 50px !important; height: 42px; border: 1.5px solid #cbd5e1; padding: 6px 18px;">
                            <small class="text-muted d-block mt-1">Max size 15MB</small>
                            <div id="existingCoaList" class="mt-2"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-white d-flex justify-content-end gap-2" style="border-top: 1.5px solid #f1f5f9;">
                    <button type="submit" class="btn text-white fw-bold px-4" id="saveDocBtn" style="background-color: #f89884; border-radius: 50px !important; font-size: 14px; padding: 9px 24px; border: none;">Save Changes</button>
                    <button type="button" class="btn text-white px-4 fw-bold" data-dismiss="modal" data-bs-dismiss="modal" style="background-color: #5a6a80; border-radius: 50px !important; font-size: 14px; padding: 9px 24px; border: none;">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL: Make Payment ================= -->
<div class="modal fade" id="makePaymentModal" tabindex="-1" aria-labelledby="makePaymentLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content" style="border-radius: 22px !important; overflow: hidden; border: none !important; box-shadow: 0 15px 35px rgba(0,0,0,0.12);">
            <div class="modal-header d-flex justify-content-between align-items-center" style="background-color: #f89884; color: #ffffff; padding: 16px 24px; border-bottom: none;">
                <h5 class="modal-title font-weight-bold" style="color: #ffffff !important; font-size: 17px; margin: 0; font-weight: 700;"><i class="fas fa-hand-holding-usd me-2"></i> Record Payment</h5>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn" id="btnOpenHistoryFromPayModal" style="background-color: #ffffff; color: #f89884; border-radius: 50px !important; font-size: 12px; font-weight: 700; padding: 5px 16px; border: none; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <i class="fas fa-history me-1"></i> Payment History
                    </button>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="color: #ffffff !important; opacity: 0.95 !important; font-size: 26px !important; font-weight: 300 !important; text-shadow: none !important; border: none !important; background: transparent !important; padding: 0 0 0 8px !important; cursor: pointer; line-height: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form id="makePaymentForm">
                @csrf
                <input type="hidden" id="payOrderId" name="order_id">
                <input type="hidden" id="payRemainingHidden" value="0">
                <div class="modal-body px-4 py-2">
                    <!-- Amount Summary Box -->
                    <div class="p-3 mb-3 rounded-4" style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px;">
                        <div style="font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                            Total Amount: <span id="payTotalAmount">&#8377;0.00</span>
                        </div>
                        <div style="font-size: 14px; font-weight: 700; color: #1e293b;">
                            Remaining Amount: <span id="payRemainingAmount">&#8377;0.00</span>
                        </div>
                    </div>

                    <!-- Select Payment Method -->
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">Select Payment Method</label>
                        <select class="form-control" name="payment_method" id="payMethodSelect" style="border-radius: 50px !important; height: 42px; border: 1.5px solid #cbd5e1; font-size: 13.5px; padding: 6px 18px;" required>
                            <option value="">Select</option>
                            <option value="Cash" selected>Cash</option>
                            <option value="Online">Online</option>
                            <option value="Cash+Online">Cash+Online</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Debit">Debit Card</option>
                        </select>
                    </div>

                    <!-- Single Payment Amount (Shown for Cash, Online, Cheque, Debit) -->
                    <div class="mb-3" id="paySingleAmountWrap">
                        <label class="form-label" style="font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">Payment Amount</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" name="amount" id="payAmountInput" style="border-radius: 50px !important; height: 42px; border: 1.5px solid #cbd5e1; font-size: 13.5px; padding: 6px 18px;" placeholder="Enter amount">
                    </div>

                    <!-- Cash + Online Split Inputs -->
                    <div class="row g-2 mb-3 d-none" id="payCashOnlineWrap">
                        <div class="col-6">
                            <label class="form-label" style="font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">Cash Amount</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="cash_amount" id="payCashAmountInput" style="border-radius: 50px !important; height: 42px; border: 1.5px solid #cbd5e1; font-size: 13.5px; padding: 6px 18px;" value="0.00">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">Online Amount</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="online_amount" id="payOnlineAmountInput" style="border-radius: 50px !important; height: 42px; border: 1.5px solid #cbd5e1; font-size: 13.5px; padding: 6px 18px;" value="0.00">
                        </div>
                    </div>

                    <!-- Payment Date & Remarks Row -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">Payment Date</label>
                            <input type="date" class="form-control" name="payment_date" id="makePayDate" value="{{ date('Y-m-d') }}" style="border-radius: 50px !important; height: 42px; border: 1.5px solid #cbd5e1; font-size: 13.5px; padding: 6px 18px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">Remarks</label>
                            <input type="text" class="form-control" name="remarks" id="makePayRemarks" placeholder="" style="border-radius: 50px !important; height: 42px; border: 1.5px solid #cbd5e1; font-size: 13.5px; padding: 6px 18px;">
                        </div>
                    </div>

                    <!-- Received By & Paid By Row -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">Received By</label>
                            <input type="text" class="form-control" name="received_by" id="makePayReceivedBy" placeholder="" style="border-radius: 50px !important; height: 42px; border: 1.5px solid #cbd5e1; font-size: 13.5px; padding: 6px 18px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">Paid By</label>
                            <input type="text" class="form-control" name="paid_by" id="makePayPaidBy" placeholder="" style="border-radius: 50px !important; height: 42px; border: 1.5px solid #cbd5e1; font-size: 13.5px; padding: 6px 18px;">
                        </div>
                    </div>
                </div>
                <!-- Footer Buttons -->
                <div class="modal-footer px-4 pb-4 pt-2 bg-white d-flex justify-content-end gap-2" style="border-top: none;">
                    <button type="submit" class="btn text-white fw-bold" id="submitPaymentBtn" style="background-color: #f89884; border-radius: 50px !important; padding: 9px 24px; font-size: 14px; border: none;">
                        Submit Payment
                    </button>
                    <button type="button" class="btn text-white fw-bold" data-dismiss="modal" data-bs-dismiss="modal" style="background-color: #5a6a80; border-radius: 50px !important; padding: 9px 24px; font-size: 14px; border: none;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL: Payment History (Matching Purchase Design) ================= -->
<div class="modal fade" id="paymentHistoryModal" tabindex="-1" aria-labelledby="historyLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 22px !important; overflow: hidden; border: none !important; box-shadow: 0 15px 35px rgba(0,0,0,0.12);">
            <div class="modal-header d-flex justify-content-between align-items-center" style="background-color: #f89884; color: #ffffff; padding: 14px 22px; border-bottom: none;">
                <h5 class="modal-title font-weight-bold" id="historyLabel" style="color: #ffffff !important; font-size: 17px; margin: 0; font-weight: 700;"><i class="fas fa-history me-2"></i> Payment History - <span id="phBillNo"></span></h5>
                <button type="button" class="btn-close-custom text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="color: #ffffff !important; opacity: 0.95 !important; font-size: 20px !important; border: none !important; background: transparent !important; padding: 0 !important; cursor: pointer; line-height: 1;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="table-responsive rounded" style="border: 1.5px solid #e2e8f0; overflow: hidden;">
                    <table class="table table-bordered mb-0" style="font-size: 13px;">
                        <thead style="background-color: #f89884; color: #ffffff;">
                            <tr>
                                <th class="text-center" style="background-color: #f89884 !important; color: #ffffff !important; width: 45px;">#</th>
                                <th style="background-color: #f89884 !important; color: #ffffff !important;">Payment Date</th>
                                <th class="text-center" style="background-color: #f89884 !important; color: #ffffff !important;">Method</th>
                                <th class="text-end" style="background-color: #f89884 !important; color: #ffffff !important;">Paid Amount</th>
                                <th class="text-end" style="background-color: #f89884 !important; color: #ffffff !important;">Remaining</th>
                                <th style="background-color: #f89884 !important; color: #ffffff !important;">Remarks</th>
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
            <div class="modal-footer p-3 bg-white d-flex justify-content-end" style="border-top: none;">
                <button type="button" class="btn text-white fw-bold px-4" data-dismiss="modal" data-bs-dismiss="modal" style="background-color: #707e94; border-radius: 50px !important; padding: 7px 24px; font-size: 13.5px; border: none;">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    // Raw Sales Data from Backend
    var allSalesList = @json($orders ?? []);
    var saleAuthToken = (typeof token !== 'undefined' && token) ? token : (localStorage.getItem('token') || '');
    var currentGstFilter = 'with_gst'; // default active tab
    var currentPage = 1;
    var perPage = 10;
    var filterDateStart = null;
    var filterDateEnd = null;

    // Format Currency Helper
    function formatMoney(num) {
        return parseFloat(num || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Format Date: e.g. 29-Oct-2026
    function formatDateDisplay(dateStr) {
        if (!dateStr) return 'N/A';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        const day = String(d.getDate()).padStart(2, '0');
        const m = months[d.getMonth()];
        const y = d.getFullYear();
        return `${day}-${m}-${y}`;
    }

    // Helper: Map Payment Method to Class & Label (All 50% radius pills)
    function getPaymentBadgeHtml(method, paymentStatus) {
        const m = (method || '').toLowerCase();
        const s = (paymentStatus || '').toLowerCase();

        if (s === 'pending' || m === 'pending' || m === 'pay_later') {
            return `<span class="status-badge badge-pending">Pending</span>`;
        }
        if (m === 'cash') {
            return `<span class="status-badge badge-cash">Cash</span>`;
        }
        if (m === 'debit') {
            return `<span class="status-badge badge-debit">Debit</span>`;
        }
        if (m === 'cash_online' || m === 'cash+online') {
            return `<span class="status-badge badge-cash_online">Cash+Online</span>`;
        }
        if (m === 'upi' || m === 'online') {
            return `<span class="status-badge badge-upi">UPI Paid</span>`;
        }
        return `<span class="status-badge badge-cash">${method || 'Paid'}</span>`;
    }

    // Main Filter Function
    function getFilteredSales() {
        const search = ($('#salesSearchInput').val() || '').toLowerCase().trim();
        const selectedPatient = $('#filterPatient').val();
        const selectedMonth = $('#filterMonth').val();
        const selectedYear = $('#filterYear').val();

        return allSalesList.filter(order => {
            // 1. GST Filter (without_gst vs with_gst)
            const orderGst = (order.gst_option || '').toLowerCase();
            const isWithGst = orderGst === 'with_gst' || orderGst === 'gst' || orderGst === 'with gst';
            if (currentGstFilter === 'with_gst' && !isWithGst) return false;
            if (currentGstFilter === 'without_gst' && isWithGst) return false;

            // 2. Patient Filter
            if (selectedPatient) {
                const patientId = order.user_id || (order.patients && order.patients.length > 0 ? order.patients[0].id : null);
                if (String(patientId) !== String(selectedPatient)) return false;
            }

            // 3. Date Filters
            const rawDate = order.order_date || order.created_at;
            if (rawDate) {
                const d = new Date(rawDate);
                if (!isNaN(d.getTime())) {
                    // Month
                    if (selectedMonth) {
                        const mStr = String(d.getMonth() + 1).padStart(2, '0');
                        if (mStr !== selectedMonth) return false;
                    }
                    // Year
                    if (selectedYear) {
                        const yStr = String(d.getFullYear());
                        if (yStr !== selectedYear) return false;
                    }
                    // Date Range
                    if (filterDateStart && filterDateEnd) {
                        const checkTime = d.setHours(0,0,0,0);
                        if (checkTime < filterDateStart || checkTime > filterDateEnd) return false;
                    }
                }
            }

            // 4. Text Search (Bill No, Patient Name, Mobile)
            if (search) {
                const billNo = (order.order_number || order.order_invoice || '').toLowerCase();
                const patName = (order.patients && order.patients.length > 0 ? order.patients[0].fullname : (order.user ? order.user.name : '')).toLowerCase();
                const phone = (order.patients && order.patients.length > 0 ? (order.patients[0].phone || order.patients[0].contact_no) : '').toLowerCase();
                const total = String(order.total_amount || '');

                if (!billNo.includes(search) && !patName.includes(search) && !phone.includes(search) && !total.includes(search)) {
                    return false;
                }
            }

            return true;
        });
    }

    // Render Table and Recalculate Summary Boxes
    function renderSalesTable() {
        const filtered = getFilteredSales();

        // 1. Recalculate Summary Boxes from currently filtered dataset
        let totalAmt = 0;
        let totalPending = 0;
        let totalPaid = 0;

        filtered.forEach(o => {
            const tot = parseFloat(o.total_amount) || 0;
            const rem = parseFloat(o.remaining_amount) || 0;
            totalAmt += tot;
            totalPending += rem;
            totalPaid += Math.max(0, tot - rem);
        });

        $('#summaryPendingAmount').html('&#8377;' + formatMoney(totalPending));
        $('#summaryPaidAmount').html('&#8377;' + formatMoney(totalPaid));
        $('#summaryTotalAmount').html('&#8377;' + formatMoney(totalAmt));
        $('#tableInfoText').text(`Showing ${filtered.length} items`);

        // 2. Pagination calculation
        const totalItems = filtered.length;
        const totalPages = Math.ceil(totalItems / perPage) || 1;
        if (currentPage > totalPages) currentPage = totalPages;

        const startIdx = (currentPage - 1) * perPage;
        const pageItems = filtered.slice(startIdx, startIdx + perPage);

        // 3. Build Table Rows
        const tbody = $('#salesTableBody');
        tbody.empty();

        if (pageItems.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                        <i class="fas fa-file-invoice fa-3x mb-2 text-warning"></i>
                        <div class="fw-bold fs-6">No sales invoices found matching current filter</div>
                    </td>
                </tr>
            `);
            $('#paginationList').empty();
            $('#paginationInfo').text('Showing 0 to 0 of 0 entries');
            return;
        }

        pageItems.forEach(order => {
            const billNo = order.order_number || order.order_invoice || `INV-${order.id}`;
            const billDate = formatDateDisplay(order.order_date || order.created_at);
            const patName = (order.patients && order.patients.length > 0) ? (order.patients[0].fullname || order.patients[0].name) : (order.user?.name || 'Walk-in Patient');
            const patPhone = (order.patients && order.patients.length > 0) ? (order.patients[0].phone || order.patients[0].contact_no || '-') : (order.user?.phone || '-');
            const patId = (order.patients && order.patients.length > 0) ? order.patients[0].id : (order.user_id || '');
            const totalStr = '&#8377;' + formatMoney(order.total_amount);
            const remaining = parseFloat(order.remaining_amount) || 0;
            const isPendingPayment = remaining > 0;

            const rowHtml = `
                <tr data-id="${order.id}">
                    <td style="text-align: center;">
                        <input type="checkbox" class="sale-checkbox" data-id="${order.id}" style="cursor: pointer; width: 16px; height: 16px;">
                    </td>
                    <td>
                        <a href="/sales-invoice/${order.id}" class="fw-bold" style="color: #002e5b; text-decoration: none;">
                            ${billNo}
                        </a>
                    </td>
                    <td>${billDate}</td>
                    <td class="fw-semibold">${patName}</td>
                    <td>${patPhone}</td>
                    <td>
                        ${getPaymentBadgeHtml(order.payment_method, order.payment_status)}
                    </td>
                    <td class="fw-bold" style="color: #1e293b;">${totalStr}</td>
                    <td>
                        <button type="button" class="btn btn-doc-orange documentation-btn" data-id="${order.id}">
                            Documentation
                        </button>
                    </td>
                    <td style="text-align: center; position: relative;">
                        <div class="action-dropdown-wrap">
                            <button class="action-menu-toggle" type="button" title="Actions">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="action-menu-box shadow">
                                ${isPendingPayment ? `
                                    <a class="dropdown-item pay-item make-payment-btn" href="javascript:void(0);" data-id="${order.id}" data-total="${order.total_amount}" data-remaining="${remaining}">
                                        <i class="fa fa-inr" style="color: #f89884; font-weight: bold; width: 16px; text-align: center;"></i> <span style="color: #f89884; font-weight: 700;">Pay</span>
                                    </a>
                                ` : ''}
                                <a class="dropdown-item open-history-btn" href="javascript:void(0);" data-id="${order.id}" data-bill="${order.invoice_number || ('#' + order.id)}">
                                    <i class="fas fa-history" style="color: #64748b; width: 16px; text-align: center;"></i> <span>History</span>
                                </a>
                                <a class="dropdown-item" href="/add-sales?patient_id=${encodeURIComponent(patId)}">
                                    <i class="fas fa-shopping-cart" style="color: #64748b; width: 16px; text-align: center;"></i> <span>New Sale</span>
                                </a>
                                <a class="dropdown-item" href="/edit-sales/${order.id}">
                                    <i class="fas fa-edit" style="color: #64748b; width: 16px; text-align: center;"></i> <span>Edit</span>
                                </a>
                                <a class="dropdown-item" href="/sales-invoice/${order.id}">
                                    <i class="fas fa-eye" style="color: #64748b; width: 16px; text-align: center;"></i> <span>Invoice</span>
                                </a>
                                <a class="dropdown-item" href="/sales-invoice/${order.id}?download_pdf=1" target="_blank">
                                    <i class="fas fa-file-pdf" style="color: #64748b; width: 16px; text-align: center;"></i> <span>Download PDF</span>
                                </a>
                                <a class="dropdown-item" href="/sales-details/${order.id}">
                                    <i class="fas fa-file-alt" style="color: #64748b; width: 16px; text-align: center;"></i> <span>Patient Details</span>
                                </a>
                                <div class="dropdown-divider my-1" style="border-top: 1px solid #f1f5f9;"></div>
                                <a class="dropdown-item text-danger delete-order-btn" href="javascript:void(0);" data-id="${order.id}">
                                    <i class="fas fa-trash-alt" style="color: #ef4444; width: 16px; text-align: center;"></i> <span style="color: #ef4444; font-weight: 600;">Delete</span>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(rowHtml);
        });

        // 4. Render Pagination
        renderPagination(totalItems, totalPages);
        updateDeleteBtnText();
    }

    // Pagination Generator
    function renderPagination(totalItems, totalPages) {
        const pagList = $('#paginationList');
        pagList.empty();

        const from = totalItems === 0 ? 0 : (currentPage - 1) * perPage + 1;
        const to = Math.min(currentPage * perPage, totalItems);
        $('#paginationInfo').text(`Showing ${from} to ${to} of ${totalItems} entries`);

        if (totalPages <= 1) return;

        pagList.append(`
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0);" data-page="${currentPage - 1}">&laquo;</a>
            </li>
        `);

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                pagList.append(`
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                    </li>
                `);
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                pagList.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
            }
        }

        pagList.append(`
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0);" data-page="${currentPage + 1}">&raquo;</a>
            </li>
        `);
    }

    // Checkbox toggling & Delete button text update
    function updateDeleteBtnText() {
        const checkedCount = $('.sale-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#deleteAllSalesBtn').html(`<i class="fas fa-trash-alt me-1"></i> Delete Selected (${checkedCount})`);
        } else {
            $('#deleteAllSalesBtn').html(`<i class="fas fa-trash-alt me-1"></i> Delete All`);
        }
    }

    $(document).ready(function() {
        // Init DateRangePicker
        if (typeof $('#filterDateRange').daterangepicker === 'function') {
            $('#filterDateRange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'DD-MM-YYYY'
                }
            });

            $('#filterDateRange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY') + ' to ' + picker.endDate.format('DD-MM-YYYY'));
                filterDateStart = picker.startDate.startOf('day').toDate().getTime();
                filterDateEnd = picker.endDate.endOf('day').toDate().getTime();
                currentPage = 1;
                renderSalesTable();
            });

            $('#filterDateRange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                filterDateStart = null;
                filterDateEnd = null;
                currentPage = 1;
                renderSalesTable();
            });
        }

        // GST Tabs Click Handler
        $('.gst-tab-item').on('click', function() {
            $('.gst-tab-item').removeClass('active');
            $(this).addClass('active');
            currentGstFilter = $(this).data('gst');
            currentPage = 1;
            renderSalesTable();
        });

        // Search Input
        $('#salesSearchInput').on('keyup input', function() {
            currentPage = 1;
            renderSalesTable();
        });

        // Dropdowns Filter
        $('#filterPatient, #filterMonth, #filterYear').on('change', function() {
            currentPage = 1;
            renderSalesTable();
        });

        // Per Page Change
        $('#perPageSelect').on('change', function() {
            perPage = parseInt($(this).val()) || 10;
            currentPage = 1;
            renderSalesTable();
        });

        // Pagination Click
        $(document).on('click', '#paginationList .page-link', function(e) {
            e.preventDefault();
            const p = parseInt($(this).data('page'));
            if (p && p !== currentPage) {
                currentPage = p;
                renderSalesTable();
                $('html, body').animate({ scrollTop: $('#salesTable').offset().top - 100 }, 200);
            }
        });

        // Select All Checkbox
        $('#selectAllCheckbox').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.sale-checkbox').prop('checked', isChecked);
            updateDeleteBtnText();
        });

        $(document).on('change', '.sale-checkbox', function() {
            const total = $('.sale-checkbox').length;
            const checked = $('.sale-checkbox:checked').length;
            $('#selectAllCheckbox').prop('checked', total > 0 && total === checked);
            updateDeleteBtnText();
        });

        // Initial Render
        renderSalesTable();

        // ================= PRODUCT DOCUMENTATION MODAL =================
        $(document).on('click', '.documentation-btn', function() {
            const orderId = $(this).data('id');
            $('#docOrderId').val(orderId);
            $('#documentationForm')[0].reset();
            $('#existingCoaList').empty();

            // Load existing documentation data
            $.ajax({
                url: '/api/sales/documentation/' + orderId,
                type: 'GET',
                headers: { 'Authorization': 'Bearer ' + saleAuthToken },
                success: function(res) {
                    if (res && res.status === 'success' && res.data) {
                        const d = res.data;
                        $('#doc_batch_number').val(d.batch_number || '');
                        if (d.mfg_date) {
                            $('#doc_mfg_date').val(d.mfg_date.substring(0, 7));
                        }
                        if (d.expiry_date) {
                            $('#doc_expiry_date').val(d.expiry_date.substring(0, 7));
                        }
                        if (d.coa_pdfs && Array.isArray(d.coa_pdfs) && d.coa_pdfs.length > 0) {
                            let links = '<div class="fw-bold" style="font-size:12px; margin-bottom:4px;">Uploaded COA Files:</div>';
                            d.coa_pdfs.forEach((url, i) => {
                                links += `<a href="${url}" target="_blank" class="badge bg-light text-dark border me-1 mb-1 p-2" style="text-decoration:none;"><i class="fas fa-file-pdf text-danger me-1"></i> File ${i+1}</a> `;
                            });
                            $('#existingCoaList').html(links);
                        }
                    }
                    $('#documentationModal').modal('show');
                },
                error: function() {
                    $('#documentationModal').modal('show');
                }
            });
        });

        // Submit Documentation
        $('#documentationForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this)[0];
            const formData = new FormData(form);
            const btn = $('#saveDocBtn');

            btn.prop('disabled', true).text('Saving...');
            $.ajax({
                url: '/api/sales/documentation/store',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Authorization': 'Bearer ' + saleAuthToken
                },
                success: function(res) {
                    btn.prop('disabled', false).text('Save Changes');
                    if (res.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Product Documentation saved successfully.',
                            icon: 'success',
                            confirmButtonColor: '#f89884'
                        });
                        $('#documentationModal').modal('hide');
                    } else {
                        Swal.fire('Error', res.message || 'Error saving documentation', 'error');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Save Changes');
                    Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.message : 'Error saving documentation', 'error');
                }
            });
        });

        // ================= 3-DOT ACTION MENU TOGGLE =================
        // Clicking anywhere in the 3-dots button toggles menu directly underneath
        $(document).on('click', '.action-dropdown-wrap, .action-menu-toggle', function(e) {
            if ($(e.target).closest('.action-menu-box').length) {
                return; // Click inside menu item, handled separately
            }
            e.preventDefault();
            e.stopPropagation();
            const $btn = $(this).is('.action-menu-toggle') ? $(this) : $(this).find('.action-menu-toggle');
            const $wrap = $(this).closest('.action-dropdown-wrap');
            const $box = $wrap.find('.action-menu-box');
            const isShown = $box.hasClass('show');

            $('.action-menu-box').removeClass('show');
            $('.action-dropdown-wrap').removeClass('active');

            if (!isShown) {
                $box.addClass('show');
                $wrap.addClass('active');
                const rect = $btn[0].getBoundingClientRect();
                const menuWidth = $box.outerWidth() || 175;
                const menuHeight = $box.outerHeight() || 220;
                const windowHeight = $(window).height();
                let topPos = rect.bottom + 4;
                if (topPos + menuHeight > windowHeight && rect.top > menuHeight) {
                    topPos = rect.top - menuHeight - 4;
                }
                let leftPos = rect.right - menuWidth;
                if (leftPos < 10) leftPos = 10;
                $box.css({
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

        // Close when clicking anywhere outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.action-dropdown-wrap').length) {
                $('.action-menu-box').removeClass('show');
                $('.action-dropdown-wrap').removeClass('active');
            }
        });

        // Close when clicking any menu item
        $(document).on('show.bs.modal', '.modal', function() {
            $('.action-menu-box').removeClass('show');
            $('.action-dropdown-wrap').removeClass('active');
        });

        $(document).on('click', '[data-dismiss="modal"], [data-bs-dismiss="modal"], .btn-close-custom', function() {
            $(this).closest('.modal').modal('hide');
        });

        $(document).on('click', '.action-menu-box .dropdown-item', function() {
            $('.action-menu-box').removeClass('show');
            $('.action-dropdown-wrap').removeClass('active');
        });

        // ================= MAKE PAYMENT MODAL =================
        $(document).on('click', '.make-payment-btn', function() {
            $('.action-menu-box').removeClass('show');
            $('.action-dropdown-wrap').removeClass('active');
            const orderId = $(this).data('id');
            const total = parseFloat($(this).data('total')) || 0;
            const remaining = parseFloat($(this).data('remaining')) || 0;

            $('#payOrderId').val(orderId);
            $('#payRemainingHidden').val(remaining);
            $('#payTotalAmount').html('&#8377;' + formatMoney(total));
            $('#payRemainingAmount').html('&#8377;' + formatMoney(remaining));

            $('#payMethodSelect').val('Cash');
            $('#payAmountInput').val(remaining.toFixed(2));
            $('#paySingleAmountWrap').removeClass('d-none');
            $('#payCashOnlineWrap').addClass('d-none');
            $('#payCashAmountInput').val('0.00');
            $('#payOnlineAmountInput').val('0.00');

            $('#makePayRemarks').val('');
            $('#makePayReceivedBy').val('');
            $('#makePayPaidBy').val('');

            const today = new Date().toISOString().split('T')[0];
            $('#makePayDate').val(today);

            $('#makePaymentModal').modal('show');
        });

        // Payment Method change inside Make Payment Modal
        $('#payMethodSelect').on('change', function() {
            const method = $(this).val();
            const remaining = parseFloat($('#payRemainingHidden').val()) || 0;

            if (method === 'Cash+Online') {
                $('#paySingleAmountWrap').addClass('d-none');
                $('#payCashOnlineWrap').removeClass('d-none');
                const half = (remaining / 2).toFixed(2);
                $('#payCashAmountInput').val(half);
                $('#payOnlineAmountInput').val((remaining - parseFloat(half)).toFixed(2));
            } else {
                $('#payCashOnlineWrap').addClass('d-none');
                $('#paySingleAmountWrap').removeClass('d-none');
                $('#payAmountInput').val(remaining.toFixed(2));
            }
        });

        // View payment history button inside Make Payment Modal
        $('#btnOpenHistoryFromPayModal').on('click', function() {
            const orderId = $('#payOrderId').val();
            $('#makePaymentModal').modal('hide');
            openPaymentHistoryModal(orderId);
        });

        // Submit Payment
        $('#makePaymentForm').on('submit', function(e) {
            e.preventDefault();
            const orderId = $('#payOrderId').val();
            const method = $('#payMethodSelect').val();
            const remaining = parseFloat($('#payRemainingHidden').val()) || 0;

            let amount = 0;
            let cashAmt = 0;
            let onlineAmt = 0;

            if (method === 'Cash+Online') {
                cashAmt = parseFloat($('#payCashAmountInput').val()) || 0;
                onlineAmt = parseFloat($('#payOnlineAmountInput').val()) || 0;
                amount = cashAmt + onlineAmt;
            } else {
                amount = parseFloat($('#payAmountInput').val()) || 0;
            }

            if (amount <= 0) {
                Swal.fire('Error', 'Please enter a valid amount greater than 0', 'error');
                return;
            }

            const btn = $('#submitPaymentBtn');
            btn.prop('disabled', true).text('Processing...');

            $.ajax({
                url: '/sales/make-payment',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_id: orderId,
                    payment_method: method,
                    amount: amount,
                    cash_amount: cashAmt,
                    online_amount: onlineAmt,
                    payment_date: $('#makePayDate').val(),
                    remarks: $('#makePayRemarks').val(),
                    received_by: $('#makePayReceivedBy').val(),
                    paid_by: $('#makePayPaidBy').val()
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Authorization': 'Bearer ' + saleAuthToken
                },
                success: function(res) {
                    btn.prop('disabled', false).text('Submit Payment');
                    if (res.status) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Payment Saved Successfully!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#makePaymentModal').modal('hide');
                        // Update in local array
                        const item = allSalesList.find(o => o.id == orderId);
                        if (item) {
                            item.remaining_amount = res.remaining_amount;
                            item.payment_status = res.payment_status;
                            if (method.toLowerCase() !== 'pending') item.payment_method = method;
                        }
                        renderSalesTable();
                    } else {
                        Swal.fire('Error', res.message || 'Error recording payment', 'error');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Submit Payment');
                    Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.message : 'Error recording payment', 'error');
                }
            });
        });

        // ================= PAYMENT HISTORY MODAL =================
        $(document).on('click', '.open-history-btn', function() {
            $('.action-menu-box').removeClass('show');
            $('.action-dropdown-wrap').removeClass('active');
            const orderId = $(this).data('id');
            const billNo = $(this).data('bill') || ('#' + orderId);
            openPaymentHistoryModal(orderId, billNo);
        });

        function openPaymentHistoryModal(orderId, billNo) {
            $('#phBillNo').text(billNo || ('#' + orderId));
            $('#phTableBody').html('<tr><td colspan="6" class="text-center py-3 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Loading payments...</td></tr>');
            $('#paymentHistoryModal').modal('show');

            $.ajax({
                url: '/api/sales/payment-history/' + orderId,
                type: 'GET',
                headers: { 'Authorization': 'Bearer ' + saleAuthToken },
                success: function(res) {
                    if (res.status && res.payments && res.payments.length > 0) {
                        let html = '';
                        res.payments.forEach((p, idx) => {
                            const pDate = formatDateDisplay(p.payment_date || p.created_at);
                            const pAmt = formatMoney(p.payment_amount);
                            const pRem = formatMoney(p.remaining_amount !== undefined && p.remaining_amount !== null ? p.remaining_amount : 0);
                            const pMode = p.payment_method ? p.payment_method.toUpperCase() : 'CASH';
                            const pRemarks = p.remarks || (idx === 0 && res.payments.length === 1 ? 'Payment on sale generation' : '-');
                            html += `
                                <tr>
                                    <td class="text-center fw-bold">${idx + 1}</td>
                                    <td>${pDate}</td>
                                    <td class="text-center"><span class="badge bg-light text-dark" style="border-radius: 50px; font-weight: 600; padding: 4px 10px;">${pMode}</span></td>
                                    <td class="text-end fw-bold" style="color: #28c76f;">₹${pAmt}</td>
                                    <td class="text-end fw-bold" style="color: #ea5455;">₹${pRem}</td>
                                    <td>${pRemarks}</td>
                                </tr>
                            `;
                        });
                        $('#phTableBody').html(html);
                    } else {
                        $('#phTableBody').html('<tr><td colspan="6" class="text-center py-3 text-muted">No payment records found.</td></tr>');
                    }
                },
                error: function() {
                    $('#phTableBody').html('<tr><td colspan="6" class="text-center py-3 text-danger">Failed to fetch payment history.</td></tr>');
                }
            });
            return;
        }

        function old_openPaymentHistoryModal(orderId) {
            $('#paymentHistoryModal').modal('show');
            $('#paymentHistoryLoading').removeClass('d-none');
            $('#paymentHistoryContent').addClass('d-none');
            $('#paymentHistoryEntriesWrap').empty();

            $.ajax({
                url: '/api/sales/payment-history/' + orderId,
                type: 'GET',
                headers: { 'Authorization': 'Bearer ' + saleAuthToken },
                success: function(res) {
                    $('#paymentHistoryLoading').addClass('d-none');
                    $('#paymentHistoryContent').removeClass('d-none');

                    // 1. Fill 4-row summary box
                    const total = parseFloat(res.total_amount) || 0;
                    const remaining = parseFloat(res.remaining_amount) || 0;
                    const paid = parseFloat(res.paid_amount) || 0;
                    const returnAmt = parseFloat(res.return_amount) || 0;

                    $('#histOrderTotal').html('&#8377;' + formatMoney(total));
                    $('#histTotalPaid').html('&#8377;' + formatMoney(paid));
                    $('#histReturnAmount').html('&#8377;' + formatMoney(returnAmt));
                    $('#histRemaining').html('&#8377;' + formatMoney(remaining));

                    // 2. Render previous payments or empty box
                    const wrap = $('#paymentHistoryEntriesWrap');
                    if (res.status && res.payments && res.payments.length > 0) {
                        let html = '<div class="d-flex flex-column gap-2 mb-3">';
                        res.payments.forEach(p => {
                            const pDate = formatDateDisplay(p.payment_date || p.created_at);
                            const pAmt = formatMoney(p.payment_amount);
                            const pMode = p.payment_method ? p.payment_method.toUpperCase() : 'CASH';
                            html += `
                                <div style="border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 10px 14px; background: #ffffff;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span style="color: #64748b; font-size: 13px;">${pDate}</span>
                                        <span style="font-weight: 700; font-size: 14px; color: #1e293b;">&#8377;${pAmt} via ${pMode}</span>
                                    </div>
                                    ${p.remarks ? `<div class="text-muted mt-1" style="font-size: 12px;"><i class="fa fa-info-circle me-1"></i>${p.remarks}</div>` : ''}
                                </div>
                            `;
                        });
                        html += '</div>';
                        wrap.html(html);
                    } else {
                        wrap.html(`
                            <div class="p-3 mb-3 text-muted" style="border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 14px;">
                                No payment history found.
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#paymentHistoryLoading').addClass('d-none');
                    $('#paymentHistoryContent').removeClass('d-none');
                    $('#paymentHistoryEntriesWrap').html(`
                        <div class="p-3 mb-3 text-danger" style="border: 1.5px solid #fecaca; border-radius: 12px; font-size: 14px;">
                            Failed to fetch payment history.
                        </div>
                    `);
                }
            });
        }

        // ================= DELETE SINGLE SALE =================
        $(document).on('click', '.delete-order-btn', function() {
            const orderId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "This sale bill will be deleted and medicine inventory will be restored!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/api/sales/delete/' + orderId,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        headers: { 'Authorization': 'Bearer ' + saleAuthToken },
                        success: function(res) {
                            if (res.status) {
                                Swal.fire('Deleted!', 'Sale bill deleted.', 'success');
                                allSalesList = allSalesList.filter(o => o.id != orderId);
                                renderSalesTable();
                            } else {
                                Swal.fire('Error', res.message || 'Error deleting sale', 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.message : 'Error deleting sale', 'error');
                        }
                    });
                }
            });
        });

        // ================= DELETE ALL / DELETE SELECTED =================
        $('#deleteAllSalesBtn').on('click', function() {
            const checkedBoxes = $('.sale-checkbox:checked');
            const checkedIds = [];
            checkedBoxes.each(function() {
                checkedIds.push($(this).data('id'));
            });

            if (checkedIds.length > 0) {
                // Delete selected
                Swal.fire({
                    title: 'Delete Selected Sales?',
                    text: `Are you sure you want to delete ${checkedIds.length} selected sale bill(s)?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ea5455',
                    confirmButtonText: 'Yes, delete selected!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/sales/delete-multiple',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ids: checkedIds
                            },
                            headers: { 'Authorization': 'Bearer ' + saleAuthToken },
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire('Deleted!', 'Selected sales deleted.', 'success');
                                    allSalesList = allSalesList.filter(o => !checkedIds.includes(o.id));
                                    $('#selectAllCheckbox').prop('checked', false);
                                    renderSalesTable();
                                } else {
                                    Swal.fire('Error', res.message || 'Error deleting sales', 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.message : 'Error deleting sales', 'error');
                            }
                        });
                    }
                });
            } else {
                // Delete all filtered sales
                const filtered = getFilteredSales();
                if (filtered.length === 0) {
                    Swal.fire('Info', 'No sales to delete in current filter.', 'info');
                    return;
                }
                const allIds = filtered.map(o => o.id);
                Swal.fire({
                    title: 'Delete All Matching Sales?',
                    text: `This will permanently delete all ${allIds.length} sale bills matching current filter!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ea5455',
                    confirmButtonText: 'Yes, delete all!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/sales/delete-multiple',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ids: allIds
                            },
                            headers: { 'Authorization': 'Bearer ' + saleAuthToken },
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire('Deleted!', 'All matching sales deleted.', 'success');
                                    allSalesList = allSalesList.filter(o => !allIds.includes(o.id));
                                    renderSalesTable();
                                } else {
                                    Swal.fire('Error', res.message || 'Error deleting sales', 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.message : 'Error deleting sales', 'error');
                            }
                        });
                    }
                });
            }
        });
    });
</script>
@endsection
