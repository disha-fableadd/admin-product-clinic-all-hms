@extends('layout.app')

@section('content')
<style>
    /* Site Theme: #f89884 and #cfece0 with 50px pill radius on fields and buttons */
    .ledger-filter-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        background: #ffffff;
    }
    .ledger-stat-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 20px 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        border: 1px solid #e2e8f0;
        border-left: 5px solid #f89884;
    }
    .ledger-stat-val {
        font-size: 24px;
        font-weight: 700;
        margin-top: 6px;
        color: #1e293b;
    }
    .form-control, .form-select {
        height: 42px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 50px !important;
        padding: 8px 18px !important;
        font-size: 13.5px !important;
        color: #000000 !important;
        background-color: #ffffff;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #f89884 !important;
        box-shadow: 0 0 0 3px rgba(248, 152, 132, 0.25) !important;
    }
    .select2-container .select2-selection--single {
        height: 42px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 50px !important;
        display: flex !important;
        align-items: center !important;
        padding-left: 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
        padding-left: 10px !important;
        color: #000000 !important;
        font-size: 13.5px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 14px !important;
    }
    .btn-search-ledger {
        height: 42px;
        background-color: #f89884 !important;
        border-color: #f89884 !important;
        color: #ffffff !important;
        font-weight: 600;
        border-radius: 50px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        box-shadow: 0 2px 6px rgba(248, 152, 132, 0.35);
        transition: all 0.2s;
    }
    .btn-search-ledger:hover {
        background-color: #e58774 !important;
        color: #ffffff !important;
    }
    .btn-pill-action {
        border-radius: 50px !important;
        font-weight: 600;
        padding: 8px 20px !important;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-print-statement {
        background-color: #cfece0 !important;
        border: 1px solid #b5e4d2 !important;
        color: #1f513f !important;
    }
    .btn-print-statement:hover {
        background-color: #bce7d6 !important;
    }
    .btn-export-pdf {
        background-color: #f89884 !important;
        border: 1px solid #f89884 !important;
        color: #ffffff !important;
    }
    .btn-export-pdf:hover {
        background-color: #e58774 !important;
        color: #ffffff !important;
    }
    .btn-export-excel {
        background-color: #10b981 !important;
        border: 1px solid #10b981 !important;
        color: #ffffff !important;
    }
    .btn-export-excel:hover {
        background-color: #059669 !important;
        color: #ffffff !important;
    }
    .badge-paid {
        background-color: #cfece0;
        color: #15803d;
        border: 1px solid #b5e4d2;
        padding: 4px 12px;
        border-radius: 50px !important;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-pending {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
        padding: 4px 12px;
        border-radius: 50px !important;
        font-size: 11px;
        font-weight: 600;
    }
    .nav-tabs .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        padding: 10px 22px;
        border-radius: 50px !important;
        margin-right: 8px;
        font-size: 13.5px;
        transition: all 0.2s;
    }
    .nav-tabs .nav-link.active {
        background-color: #f89884 !important;
        color: #ffffff !important;
    }
    .ledger-table th, .ledger-table thead th {
        background-color: #f8fafc;
        color: #334155;
        font-size: 13px;
        font-weight: 700;
        padding: 12px 16px;
        border-bottom: 2px solid #e2e8f0;
        text-align: center !important;
        vertical-align: middle !important;
    }
    .ledger-table td, .ledger-table tbody td {
        padding: 11px 16px;
        font-size: 13px;
        text-align: center !important;
        vertical-align: middle !important;
        border-top: 1px solid #f1f5f9;
    }
    .ledger-table th, .ledger-table td, .ledger-table * {
        vertical-align: middle !important;
    }
    @media print {
        .no-print, .sidebar, .header {
            display: none !important;
        }
        .page-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }
    }
</style>

<div class="page-wrapper">
    <div class="content" style="padding-top: 22px !important;">
        <!-- Top Title & Export Action Bar -->
        <div class="row pt-2 pb-3 align-items-center no-print">
            <div class="col-md-5 col-12 mb-2 mb-md-0">
                <h4 class="page-title mb-0"><i class="fas fa-book me-2" style="color: #f89884;"></i> Account Ledger</h4>
                <small class="text-muted">Synchronized stock & ledger for Customers (Patients) and Vendors (Suppliers)</small>
            </div>
            <div class="col-md-7 col-12 text-md-right d-flex flex-wrap justify-content-md-end gap-2" style="gap:10px">
                <button type="button" class="btn btn-pill-action btn-export-excel" id="exportExcelBtn">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <button type="button" class="btn btn-pill-action btn-export-pdf" id="exportPdfBtn">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
               
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card ledger-filter-card no-print mb-4">
            <div class="card-body p-4">
                <div class="row align-items-end g-3">
                    <!-- 1. Ledger Type -->
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label fw-bold" style="font-size: 13px; color: #212b36;">Ledger Type</label>
                        <select class="form-control" id="ledgerType">
                            <option value="patient" selected>Patient</option>
                            <option value="vendor">Vendor</option>
                        </select>
                    </div>

                    <!-- 2. Patient Dropdown -->
                    <div class="col-md-3 col-sm-6" id="patientDropdownContainer">
                        <label class="form-label fw-bold" style="font-size: 13px; color: #212b36;">Select Patient</label>
                        <select class="form-control select2" id="patientSelect">
                            <option value="">-- Select Patient --</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}">{{ $p->fullname }} ({{ $p->phone ?: 'No Phone' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Vendor Dropdown -->
                    <div class="col-md-3 col-sm-6" id="vendorDropdownContainer" style="display: none;">
                        <label class="form-label fw-bold" style="font-size: 13px; color: #212b36;">Select Vendor</label>
                        <select class="form-control select2" id="vendorSelect">
                            <option value="">-- Select Vendor --</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->phone ?: 'No Phone' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3. Month Filter -->
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label fw-bold" style="font-size: 13px; color: #212b36;">Month</label>
                        <select class="form-control" id="filterMonth">
                            <option value="all">All Months</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- 4. Year Filter -->
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label fw-bold" style="font-size: 13px; color: #212b36;">Year</label>
                        <select class="form-control" id="filterYear">
                            <option value="all">All Years</option>
                            @foreach($years as $yr)
                                <option value="{{ $yr }}" {{ $yr == date('Y') ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>



                    <!-- 6. Search Button -->
                    <div class="col-md-2 col-sm-6">
                        <button class="btn btn-search-ledger w-100" id="fetchLedgerBtn">
                            <i class="fas fa-search"></i> View Ledger
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Select Prompt Section (Shown when no entity selected) -->
        <div id="selectPromptSection" class="card text-center p-5 mb-4 shadow-sm" style="border-radius: 16px; border: 1.5px dashed #f89884; background: #fffaf9;">
            <div style="width: 64px; height: 64px; background: #fed9cf; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto 14px;">
                <i class="fas fa-user-check" style="font-size: 26px; color: #f89884;"></i>
            </div>
            <h4 class="fw-bold" style="color: #212b36;" id="promptTitle">No Patient Selected</h4>
            <p class="text-muted mb-0" id="promptText" style="font-size: 13.5px; max-width: 580px; margin: 0 auto;">
                Please select a patient from the dropdown above and click <strong>View Ledger</strong> to generate their account statement, outstanding dues, and transaction entries.
            </p>
        </div>

        <!-- Statement Container -->
        <div id="statementSection" style="display: none;">
            <!-- Header Info -->
            <div class="card mb-3 ledger-filter-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h4 id="entityName" class="mb-1 fw-bold" style="color: #f89884;">Patient Name</h4>
                            <p id="entityDetails" class="text-muted mb-0">Phone: - | Email: -</p>
                        </div>
                        <div class="text-md-end mt-2 mt-md-0">
                            <span class="badge p-2 border" id="statementPeriod" style="background-color: #cfece0; color: #1f513f; border-radius: 50px;">Statement Period: All Time</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary KPI Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="ledger-stat-card" style="border-left-color: #3b82f6;">
                        <small class="text-muted fw-bold text-uppercase" id="kpiInvoicedLabel">Total Invoiced</small>
                        <div class="ledger-stat-val text-primary" id="kpiTotalInvoiced">₹0.00</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="ledger-stat-card" style="border-left-color: #10b981;">
                        <small class="text-muted fw-bold text-uppercase">Total Paid / Received</small>
                        <div class="ledger-stat-val text-success" id="kpiTotalPaid">₹0.00</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="ledger-stat-card" style="border-left-color: #ef4444;">
                        <small class="text-muted fw-bold text-uppercase">Outstanding Balance</small>
                        <div class="ledger-stat-val text-danger" id="kpiBalance">₹0.00</div>
                    </div>
                </div>
            </div>

            <!-- Nav Tabs -->
            <ul class="nav nav-tabs no-print border-0 mb-3" id="ledgerTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-ledger-link" data-toggle="tab" href="#tab-ledger" role="tab">
                        <i class="fas fa-list-alt me-1"></i> Account Ledger Statement
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-pending-link" data-toggle="tab" href="#tab-pending" role="tab">
                        <i class="fas fa-clock me-1"></i> Pending / Unpaid Invoices
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-paid-link" data-toggle="tab" href="#tab-paid" role="tab">
                        <i class="fas fa-check-circle me-1"></i> Paid Invoices & Receipts
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Tab 1: Comprehensive Chronological Ledger Table -->
                <div class="tab-pane fade show active" id="tab-ledger" role="tabpanel">
                    <div class="card ledger-filter-card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="card-title mb-0 fw-bold" style="color: #1e293b;"><i class="fas fa-balance-scale me-2" style="color: #f89884;"></i> Chronological Ledger Entries</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table ledger-table mb-0" id="mainLedgerTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Particulars</th>
                                            <th>Vch Type</th>
                                            <th>Vch No</th>
                                            <th>Debit (₹)</th>
                                            <th>Credit (₹)</th>
                                            <th>Balance (₹)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ledgerTableBody">
                                        <tr><td colspan="7" class="text-center text-muted py-4">No ledger records found</td></tr>
                                    </tbody>
                                    <tfoot id="ledgerTableFoot">
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Pending Invoices -->
                <div class="tab-pane fade" id="tab-pending" role="tabpanel">
                    <div class="card ledger-filter-card">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0 text-danger fw-bold"><i class="fas fa-clock me-2"></i> Pending / Unpaid Invoices</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table ledger-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Invoice / Bill No</th>
                                            <th>Date</th>
                                            <th>Items / Details</th>
                                            <th>Status</th>
                                            <th>Pending Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pendingTableBody">
                                        <tr><td colspan="5" class="text-center text-muted py-4">No pending invoices</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Paid Invoices & Receipts -->
                <div class="tab-pane fade" id="tab-paid" role="tabpanel">
                    <div class="card ledger-filter-card">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0 text-success fw-bold"><i class="fas fa-check-circle me-2"></i> Paid Invoices & Receipts</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table ledger-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Invoice / Bill No</th>
                                            <th>Date</th>
                                            <th>Items / Details</th>
                                            <th>Payment Method</th>
                                            <th>Paid Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paidTableBody">
                                        <tr><td colspan="5" class="text-center text-muted py-4">No paid records found</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');

        if ($.fn.select2) {
            $('.select2').select2({ width: '100%' });
        }

        // Toggle Type
        $('#ledgerType').on('change', function() {
            $('#statementSection').hide();
            $('#selectPromptSection').show();

            if ($(this).val() === 'patient') {
                $('#patientDropdownContainer').show();
                $('#vendorDropdownContainer').hide();
                $('#kpiInvoicedLabel').text('Total Invoiced');
                $('#promptTitle').text('No Patient Selected');
                $('#promptText').html('Please select a patient from the dropdown above and click <strong>View Ledger</strong> to generate their account statement, outstanding dues, and transaction entries.');
                if ($('#patientSelect').val()) {
                    $('#patientSelect').val('').trigger('change');
                }
            } else {
                $('#patientDropdownContainer').hide();
                $('#vendorDropdownContainer').show();
                $('#kpiInvoicedLabel').text('Total Purchases');
                $('#promptTitle').text('No Vendor Selected');
                $('#promptText').html('Please select a vendor from the dropdown above and click <strong>View Ledger</strong> to generate their purchase ledger statement, payment records, and balances.');
                if ($('#vendorSelect').val()) {
                    $('#vendorSelect').val('').trigger('change');
                }
            }
        });

        function getFilterParams() {
            const type = $('#ledgerType').val();
            const id = type === 'patient' ? $('#patientSelect').val() : $('#vendorSelect').val();
            const month = $('#filterMonth').val();
            const year = $('#filterYear').val();
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            return { type, id, month, year, start_date: startDate, end_date: endDate };
        }

        function loadLedger() {
            const params = getFilterParams();

            if (!params.id || params.id === 'all' || params.id === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select a specific ' + (params.type === 'patient' ? 'patient' : 'vendor') + ' to view their account ledger statement.',
                    confirmButtonColor: '#f89884'
                });
                $('#statementSection').hide();
                $('#selectPromptSection').show();
                return;
            }

            $('#fetchLedgerBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');

            $.ajax({
                url: '{{ url("api/account-ledger/details") }}',
                type: 'GET',
                headers: { 'Authorization': 'Bearer ' + token },
                data: params,
                success: function(res) {
                    $('#fetchLedgerBtn').prop('disabled', false).html('<i class="fas fa-search"></i> View Ledger');
                    if (res.status) {
                        $('#selectPromptSection').hide();
                        $('#statementSection').slideDown();

                        $('#entityName').text(res.entity.name);
                        let detailText = 'Phone: ' + (res.entity.phone || 'N/A');
                        if (res.entity.gstin && res.entity.gstin !== 'N/A') detailText += ' | GSTIN: ' + res.entity.gstin;
                        if (res.entity.email && res.entity.email !== 'N/A') detailText += ' | Email: ' + res.entity.email;
                        if (res.entity.address && res.entity.address !== 'N/A') detailText += ' | Address: ' + res.entity.address;
                        $('#entityDetails').text(detailText);

                        let periodText = 'Period: ';
                        if (params.start_date && params.end_date) {
                            periodText += params.start_date + ' to ' + params.end_date;
                        } else if (params.month !== 'all' || params.year !== 'all') {
                            let mText = params.month !== 'all' ? $('#filterMonth option:selected').text() : 'All Months';
                            let yText = params.year !== 'all' ? params.year : 'All Years';
                            periodText += mText + ' ' + yText;
                        } else {
                            periodText += 'All Time';
                        }
                        $('#statementPeriod').text(periodText);

                        $('#kpiTotalInvoiced').text('₹' + (res.total_invoiced || 0).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                        $('#kpiTotalPaid').text('₹' + (res.total_paid || 0).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                        $('#kpiBalance').text('₹' + (res.closing_balance || 0).toLocaleString('en-IN', {minimumFractionDigits: 2}));

                        // 1. Render Chronological Ledger Entries
                        let ledgerRows = '';
                        let openBal = parseFloat(res.opening_balance || 0);

                        if (openBal !== 0) {
                            let openDebit = res.type === 'vendor' ? '-' : ('₹' + openBal.toFixed(2));
                            let openCredit = res.type === 'vendor' ? ('₹' + openBal.toFixed(2)) : '-';
                            ledgerRows += `
                                <tr style="background: #fff8f6; font-weight: bold;">
                                    <td>-</td>
                                    <td>Opening Balance</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td class="text-end">${openDebit}</td>
                                    <td class="text-end">${openCredit}</td>
                                    <td class="text-end">₹${openBal.toFixed(2)}</td>
                                </tr>
                            `;
                        }

                        if (res.ledger_entries && res.ledger_entries.length > 0) {
                            res.ledger_entries.forEach(row => {
                                let debitDisplay = row.debit > 0 ? ('₹' + parseFloat(row.debit).toFixed(2)) : '-';
                                let creditDisplay = row.credit > 0 ? ('₹' + parseFloat(row.credit).toFixed(2)) : '-';
                                let balanceDisplay = '₹' + parseFloat(row.balance).toFixed(2);
                                ledgerRows += `
                                    <tr>
                                        <td><strong>${row.date}</strong></td>
                                        <td>${row.particulars}</td>
                                        <td><span class="badge" style="background: #e2e8f0; color: #334155;">${row.vch_type}</span></td>
                                        <td><strong>${row.vch_no}</strong></td>
                                        <td class="text-end text-dark">${debitDisplay}</td>
                                        <td class="text-end text-dark">${creditDisplay}</td>
                                        <td class="text-end fw-bold" style="color: #f89884;">${balanceDisplay}</td>
                                    </tr>
                                `;
                            });
                            $('#ledgerTableBody').html(ledgerRows);
                        } else {
                            if (openBal === 0) {
                                $('#ledgerTableBody').html('<tr><td colspan="7" class="text-center text-muted py-4">No ledger records found for the selected criteria.</td></tr>');
                            } else {
                                $('#ledgerTableBody').html(ledgerRows);
                            }
                        }

                        // Ledger Footer Totals
                        let footHtml = `
                            <tr style="background: #f8fafc; font-weight: bold; border-top: 2px solid #cbd5e1;">
                                <td colspan="4" class="text-end">Total Transactions:</td>
                                <td class="text-end text-primary">₹${(res.type === 'vendor' ? res.total_paid : res.total_invoiced).toFixed(2)}</td>
                                <td class="text-end text-success">₹${(res.type === 'vendor' ? res.total_invoiced : res.total_paid).toFixed(2)}</td>
                                <td class="text-end"></td>
                            </tr>
                            <tr style="background: #fff1ef; font-weight: 800; color: #f89884; font-size: 14px;">
                                <td colspan="4" class="text-end">Closing / Outstanding Balance:</td>
                                <td colspan="3" class="text-end">₹${parseFloat(res.closing_balance || 0).toFixed(2)}</td>
                            </tr>
                        `;
                        $('#ledgerTableFoot').html(footHtml);

                        // 2. Render Pending Table
                        if (res.pending_invoices && res.pending_invoices.length > 0) {
                            let pRows = '';
                            res.pending_invoices.forEach(row => {
                                pRows += `
                                    <tr>
                                        <td><strong>${row.invoice_no}</strong></td>
                                        <td>${row.date}</td>
                                        <td>${row.summary}</td>
                                        <td><span class="badge-pending">Pending</span></td>
                                        <td class="text-end text-danger fw-bold">₹${parseFloat(row.amount).toFixed(2)}</td>
                                    </tr>
                                `;
                            });
                            $('#pendingTableBody').html(pRows);
                        } else {
                            $('#pendingTableBody').html('<tr><td colspan="5" class="text-center text-muted py-4">No pending / unpaid invoices found.</td></tr>');
                        }

                        // 3. Render Paid Table
                        if (res.paid_invoices && res.paid_invoices.length > 0) {
                            let paidRows = '';
                            res.paid_invoices.forEach(row => {
                                paidRows += `
                                    <tr>
                                        <td><strong>${row.invoice_no}</strong></td>
                                        <td>${row.date}</td>
                                        <td>${row.summary}</td>
                                        <td><span class="badge bg-secondary text-white">${row.payment_type}</span></td>
                                        <td class="text-end text-success fw-bold">₹${parseFloat(row.amount || row.paid_amount || 0).toFixed(2)}</td>
                                    </tr>
                                `;
                            });
                            $('#paidTableBody').html(paidRows);
                        } else {
                            $('#paidTableBody').html('<tr><td colspan="5" class="text-center text-muted py-4">No paid records found.</td></tr>');
                        }
                    }
                },
                error: function(err) {
                    $('#fetchLedgerBtn').prop('disabled', false).html('<i class="fas fa-search"></i> View Ledger');
                    Swal.fire('Error', err.responseJSON ? err.responseJSON.message : 'Error fetching ledger', 'error');
                }
            });
        }

        $('#fetchLedgerBtn').on('click', loadLedger);

        // Export PDF
        $('#exportPdfBtn').on('click', function() {
            const params = getFilterParams();
            if (!params.id || params.id === 'all' || params.id === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select a specific ' + (params.type === 'patient' ? 'patient' : 'vendor') + ' to export their ledger.',
                    confirmButtonColor: '#f89884'
                });
                return;
            }
            const query = $.param(params);
            window.open('{{ url("account-ledger/export-pdf") }}?' + query, '_blank');
        });

        // Export Excel
        $('#exportExcelBtn').on('click', function() {
            const params = getFilterParams();
            if (!params.id || params.id === 'all' || params.id === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select a specific ' + (params.type === 'patient' ? 'patient' : 'vendor') + ' to export their ledger.',
                    confirmButtonColor: '#f89884'
                });
                return;
            }
            const query = $.param(params);
            window.location.href = '{{ url("account-ledger/export-excel") }}?' + query;
        });

        // Initial state: do not load overall statement when no entity is selected
    });
</script>
@endsection
