@extends('layout.app')

@section('title', 'Expense Report')

@section('content')
<style>
    :root {
        --theme-orange: #f89884;
        --theme-orange-dark: #ea7a63;
        --theme-navy: #002e5b;
        --theme-mint: #cfece0;
        --theme-dark: #1e293b;
    }

    /* Clinic Theme Card & Table Header */
    .card {
        border: none !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04) !important;
        background: #ffffff !important;
    }

    table.datanew thead tr th, table thead tr th {
        background-color: #f89884 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        padding: 12px 14px !important;
        border: none !important;
        vertical-align: middle !important;
    }
    table.datanew thead tr th:first-child {
        border-top-left-radius: 12px !important;
    }
    table.datanew thead tr th:last-child {
        border-top-right-radius: 12px !important;
    }

    /* Filters (Pill design matching other clinic pages) */
    .search-set select, .search-set .form-select {
        height: 38px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 50px !important;
        padding: 6px 18px !important;
        font-size: 13px !important;
        color: #1e293b !important;
        outline: none !important;
        transition: all 0.2s ease !important;
    }
    .search-set select:focus, .search-set .form-select:focus {
        border-color: #f89884 !important;
        box-shadow: 0 0 0 2px rgba(248, 152, 132, 0.25) !important;
    }

    /* Total Summary Pill */
    .total_expense, #total-expense-amount {
        background: #fff5f2 !important;
        border: 1.5px solid #f89884 !important;
        color: #d9534f !important;
        border-radius: 50px !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        padding: 6px 18px !important;
        display: inline-flex !important;
        align-items: center !important;
    }

    /* Buttons */
    #generate-pdf, #generate-pdf button, .wordset .btn, .btn-primary {
        background-color: #f89884 !important;
        border-color: #f89884 !important;
        border-radius: 50px !important;
        font-weight: 600 !important;
        color: #ffffff !important;
        padding: 7px 20px !important;
        transition: all 0.2s ease !important;
    }
    #generate-pdf:hover, #generate-pdf button:hover, .wordset .btn:hover, .btn-primary:hover {
        background-color: #f67e65 !important;
        border-color: #f67e65 !important;
        transform: translateY(-1px) !important;
    }

    /* Hover Rows */
    table.datanew tbody tr:hover td {
        background-color: #fff9f8 !important;
    }

    /* Pagination */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #f89884 !important;
        border-color: #f89884 !important;
        color: #ffffff !important;
        border-radius: 50px !important;
    }
    .expense-chart-wrapper {
        position: relative;
        /* 🔑 KEY FIX */
        height: 350px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #noExpenseMessage {
        position: absolute;
        inset: 0;
        /* top:0 right:0 bottom:0 left:0 */
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6b7280;
        font-size: 16px;
        background: #fff;
        z-index: 5;
    }

    .dataTables_info {
        float: left;
        padding-right: 15px;
        font-size: 12px;
        color: #5e5873;
        font-weight: 600;
    }

    .table-scroll-top {
        display: none;
    }

    .table-scroll-top {
        overflow-x: auto;
        overflow-y: hidden;
        height: 20px;
        width: 100%;
        margin-bottom: 5px;
    }

    .table-scroll-top div {
        height: 1px;
    }

    #expenseChart {
        width: 100% !important;
        height: 100% !important;
        max-height: 320px;
    }

    .total_expense {
        font-weight: 600;
        color: #1b2850;
        border: 1px solid #1b2850;
    }

    /* Force hide toggle-details by default */
    .toggle-details {
        display: none !important;
    }

    /* Desktop: show all columns normally, HIDE details column */
    @media (min-width: 769px) {

        table.datanew thead th,
        table.datanew tbody td {
            display: table-cell !important;
        }

        /* Hide the Details toggle column on desktop */
        table.datanew thead th.details-column,
        table.datanew tbody td.details-column {
            display: none !important;
        }

        /* Ensure checkbox column is visible */
        table.datanew thead th:first-child,
        table.datanew tbody td:first-child {
            display: table-cell !important;
        }
    }

    /* Mobile: hide non-essential columns, show Details toggle */
    @media (max-width: 768px) {

        /* Override DataTables responsive classes */
        table.datanew th.dt-control,
        table.datanew td.dt-control {
            display: none !important;
        }

        /* Hide columns 3, 4, 5 on mobile (Amount, Date, Expense For) */
        table.datanew thead th:nth-child(3),
        table.datanew tbody td:nth-child(3),
        table.datanew thead th:nth-child(4),
        table.datanew tbody td:nth-child(4),
        table.datanew thead th:nth-child(5),
        table.datanew tbody td:nth-child(5) {
            display: none !important;
        }

        /* Show checkbox column on mobile */
        table.datanew thead th:first-child,
        table.datanew tbody td:first-child {
            display: table-cell !important;
            width: 40px;
            min-width: 40px;
            max-width: 40px;
        }

        /* Show Expense Name column on mobile */
        table.datanew thead th:nth-child(2),
        table.datanew tbody td:nth-child(2) {
            display: table-cell !important;
            min-width: calc(100vw - 140px) !important;
            max-width: calc(100vw - 140px) !important;
        }

        /* Show details column on mobile */
        table.datanew thead th.details-column,
        table.datanew tbody td.details-column {
            display: table-cell !important;
            text-align: center;
            vertical-align: top !important;
            padding-top: 25px !important;
            width: 50px;
            min-width: 50px;
            max-width: 50px;
        }

        /* Show toggle-details only on mobile */
        .toggle-details {
            display: inline-block !important;
        }

        .toggle-details i {
            font-size: 18px;
        }

        /* Style for expense name wrapping */
        table.datanew tbody td:nth-child(2) {
            display: flex !important;
            align-items: center !important;
        }

        table.datanew tbody td:nth-child(2) a {
            align-items: center !important;
            text-align: left !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            white-space: normal !important;
            line-height: 1.3 !important;
            text-decoration: none !important;
            color: #212529 !important;
            font-weight: 500;
            width: 100%;
        }

        .expense-name {
            display: inline-block !important;
            font-size: 14px !important;
            word-break: break-word !important;
            hyphens: auto !important;
            -webkit-hyphens: auto !important;
            -ms-hyphens: auto !important;
            width: 100%;
        }

        /* Limit to 2 lines with ellipsis */
        .expense-name.truncated {
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        /* Make sure checkbox is visible and properly aligned */
        table.datanew tbody td:first-child .checkboxs {
            display: block;
            margin: 0 auto;
            text-align: center;
        }

        table.datanew tbody td:first-child {
            padding: 8px 5px !important;
        }
    }

    @media screen and (max-width: 768px) {
        .table-scroll-top {
            display: block;
            -webkit-overflow-scrolling: touch !important;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch !important;
        }

        .search-set {
            margin-right: 1rem !important;
        }

        /* Adjust filter layout for mobile */
        .table-top .search-set {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-start !important;
            width: 100%;
        }

        .search-set .form-select-sm {
            flex: 1;
            min-width: 120px;
        }

        .wordset {
            width: 100%;
            margin-top: 10px !important;
        }

        .wordset ul {
            width: 100%;
        }

        .wordset ul li {
            width: 100%;
        }

        .wordset ul li a button {
            width: 100%;
        }

        #expenseType-filter {
            min-width: 150px !important;
        }
    }

    /* Tablet specific fixes */
    @media screen and (width: 768px) {
        .table-responsive {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }

        table.datanew thead th.details-column,
        table.datanew tbody td.details-column {
            display: table-cell !important;
            width: 60px !important;
            min-width: 60px !important;
            max-width: 60px !important;
        }

        .toggle-details {
            display: inline-block !important;
            padding: 8px !important;
            z-index: 10 !important;
        }

        .toggle-details i {
            font-size: 20px !important;
            width: 24px !important;
            height: 24px !important;
            line-height: 24px !important;
        }
    }

    /* Fade out animation for error messages */
    .fade-out {
        opacity: 1;
        transition: opacity 0.5s ease-out;
    }

    .fade-out.hidden {
        opacity: 0;
    }

    /* Collapsible details styling */
    .collapse-details {
        margin-top: 10px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
        border-left: 3px solid #ff9f43;
    }

    .detail-item {
        display: flex;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .detail-label {
        font-weight: 600;
        min-width: 100px;
        color: #495057;
    }

    .detail-value {
        color: #212529;
        flex: 1;
    }

    /* Mobile view for total expense */
    @media (max-width: 768px) {
        .page-header {
            flex-wrap: wrap;
        }

        .total-expense-summary {
            /* width: 100%; */
            margin-top: 10px;
        }

        #total-expense-amount {
            display: inline-block;
            margin-left: 0 !important;
        }
    }

    /* Expense specific styling */
    .expense-amount-badge {
        display: inline-block;
        padding: 4px 10px;
        background-color: #e8f5e9;
        color: #2e7d32;
        border-radius: 12px;
        font-weight: 600;
        font-size: 13px;
        margin-left: 8px;
    }
</style>

@if (session('error'))
<div class="alert alert-danger" id="error-message">
    {{ session('error') }}
</div>
@endif

<div class="page-wrapper">
    <div class="content px-3 px-md-4 pt-3 pb-4">
        <!-- Main Title / Banner Card matching other clinic pages -->
        <div class="card border-0 mb-4 table-container-card overflow-hidden" style="border-radius: 16px;">
            <div class="card-header" style="background-color: #f89884 !important; border-bottom: none !important; padding: 14px 22px !important; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; border-top-left-radius: 16px !important; border-top-right-radius: 16px !important;">
                <h3 class="card-title d-inline-block text-white mb-0" style="font-weight: 700; font-size: 19px;">
                    <i class="fa fa-money-bill-wave px-2"></i> Expense Report
                </h3>
                <div class="d-flex align-items-center gap-2 flex-wrap" style="gap:10px">
                    <span id="total-expense-amount" class="total-pill" style="background:#ffffff !important; color:#d9534f !important; font-weight:700 !important; font-size:13.5px !important; border-radius:50px !important; padding:6px 18px !important; display:inline-flex !important; align-items:center !important; box-shadow:0 2px 6px rgba(0,0,0,0.06);">
                        Total: ₹0.00
                    </span>
                    <button id="generate-pdf" class="btn btn-hdr" style="background:#ffffff !important; color:#f89884 !important; font-weight:700 !important; font-size:13px !important; border-radius:50px !important; padding:6px 18px !important; border:none !important; display:inline-flex !important; align-items:center !important; gap:6px !important; box-shadow:0 2px 6px rgba(0,0,0,0.06);">
                        <i class="fa-solid fa-file-pdf"></i> View PDF
                    </button>
                </div>
            </div>
        </div>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap">
            <h6 class="fw-bold mb-0 text-secondary d-flex align-items-center">
                <i class="fa-solid fa-chart-column me-2 text-primary"></i>
                Expense Summary
            </h6>

            <div id="expense-filter-summary"
                style="
                        display:none;
                        background:#f9fafb;
                        border:1px solid #e5e7eb;
                        border-radius:6px;
                        padding:6px 10px;
                        font-size:14px;
                        font-weight:500;
                        color:#374151;
                        align-items:center;
                        justify-content:center;
                        gap:6px;
                        white-space:nowrap;
                        transition:all 0.3s ease;
                    ">
            </div>
        </div>

        <!-- <div class="card-body d-flex align-items-center justify-content-center" style="height: 350px;"> -->
        <div class="card-body expense-chart-wrapper">
            <canvas id="expenseChart" style="max-height: 320px; width: 100%;"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-top">
                <div class="search-set">
                    <select id="date-filter" class="form-select form-select-sm">
                        <option value="">All Time</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                        <option value="last_6_months">Last 6 Months</option>
                        <option value="this_year">This Year</option>
                        <option value="previous_year">Previous Year</option>
                    </select>
                    <select id="month-filter" class="form-select form-select-sm"
                        style="margin-right:8px; margin-left: 8px;">
                        <option value="">All Months</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                    </select>

                    <select id="year-filter" class="form-select form-select-sm" style="margin-right:8px;">
                        <option value="">All Years</option>
                        @for ($y = date('Y'); $y >= date('Y') - 10; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>

                    <select id="expenseType-filter" class="form-select form-select-sm"
                        style="margin-right:8px; min-width: 180px;">
                        <option value="">All Expense Services</option>
                        @isset($expenseTypes)
                        @foreach ($expenseTypes as $expenseType)
                        <option value="{{ $expenseType->id ?? $expenseType->type }}">{{ $expenseType->type ?? $expenseType->name }}</option>
                        @endforeach
                        @endisset
                        @isset($expenseServices)
                        @foreach ($expenseServices as $service)
                        <option value="{{ $service }}">{{ $service }}</option>
                        @endforeach
                        @endisset
                    </select>
                </div>
                <div class="wordset">
                    <ul>
                        <li>
                            <a id="generate-pdf" href="javascript:void(0);" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Download PDF">
                                <button class="btn btn-primary btn-sm">View PDF</button>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="table-scroll-top">
                <div></div>
            </div>
            <div class="table-responsive">
                <table class="table datanew">
                    <thead>
                        <tr>
                            <th>
                                <label class="checkboxs">
                                    <input type="checkbox" id="select-all">
                                    <span class="checkmarks"></span>
                                </label>
                            </th>
                            <th>Expense Name</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Expense For</th>
                            <th class="details-column">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        var authToken = localStorage.getItem("token") || localStorage.getItem("authToken") || @json(session('access_token')) || "";
        const selectedSubAdminId = localStorage.getItem("selectedBranchId") || localStorage.getItem("selectedSubAdminId") || "";

        var table = $('.datanew').DataTable({
            destroy: true,
            paging: true,
            searching: true,
            ordering: true,
            responsive: true,
            language: {
                emptyTable: "No expenses found.",
                zeroRecords: "No expense record found.",
                search: "Search:",
                searchPlaceholder: ""
            }
        });

        // Initial load
        fetchExpenses();
        loadExpenseChart();

        // Handle filter changes
        $('#date-filter, #month-filter, #year-filter, #expenseType-filter').on('change', function() {
            const filter = $('#date-filter').val();
            const month = $('#month-filter').val();
            const year = $('#year-filter').val();
            const expenseTypeId = $('#expenseType-filter').val();

            fetchExpenses(filter, month, year, expenseTypeId);
            loadExpenseChart(filter, month, year, expenseTypeId);
        });

        /** ------------------------------------------
         * 📋 Fetch Expense Table Data + Total
         * ------------------------------------------ */
        function fetchExpenses(filter = '', month = '', year = '', expenseTypeId = '') {
            $.ajax({
                url: "{{ url('/api/expenses/report') }}",
                method: 'GET',
                data: {
                    filter: filter,
                    month: month,
                    year: year,
                    expense_type_id: expenseTypeId,
                    selectedSubAdminId: selectedSubAdminId,
                },
                headers: {
                    "Authorization": "Bearer " + authToken,
                },
                success: function(response) {
                    table.clear().draw();
                    let totalAmount = 0;

                    // Get currency settings
                    const currencySymbol = response.currency_symbol || '₹';
                    const currencyPosition = response.currency_position || 'left';

                    // ✅ Build rows & sum total
                    $.each(response.data, function(index, expense) {
                        totalAmount += parseFloat(expense.amount || 0);

                        // Details toggle for mobile
                        let detailsToggle = `
                                <a href="#expense-details-${expense.id}" class="toggle-details" data-bs-toggle="collapse">
                                    <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                </a>
                            `;

                        // Format amount with currency
                        const amountValue = parseFloat(expense.amount || 0).toFixed(2);
                        const formattedAmount = currencyPosition === 'left' ?
                            `${currencySymbol}${amountValue}` :
                            `${amountValue}${currencySymbol}`;

                        table.row.add([
                            // Column 1: Checkbox
                            `<label class="checkboxs">
                                    <input type="checkbox" class="expense-check" name="item_ids[]" value="${expense.id}">
                                    <span class="checkmarks"></span>
                                </label>`,

                            // Column 2: Expense Name with collapsible details
                            `<div>
                                    <div style="display: flex; align-items: center;">
                                        <a href="{{ Route::has('expense.index') ? route('expense.index') : url('/expenses') }}" class="d-flex align-items-center" style="text-decoration: none; color: inherit;">
                                            <span class="expense-name truncated">${expense.expense_name ?? 'N/A'}</span>
                                        </a>
                                    </div>
                                    
                                    <!-- Collapsible Details (visible only on mobile) -->
                                    <div class="collapse mt-2 d-lg-none" id="expense-details-${expense.id}">
                                        <div class="collapse-details">
                                            <div class="detail-item">
                                                <span class="detail-label">Amount:</span>
                                                <span class="detail-value">${formattedAmount}</span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Date:</span>
                                                <span class="detail-value">${formatDate(expense.expense_date)}</span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Expense For:</span>
                                                <span class="detail-value">${expense.description ?? 'N/A'}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>`,

                            // Column 3: Amount
                            `<span class="fw-semibold" style="color: #2e7d32;">${formattedAmount}</span>`,

                            // Column 4: Date
                            formatDate(expense.expense_date),

                            // Column 5: Description
                            `<div style="white-space: normal; word-break: break-word; max-width: 400px;">${expense.description ?? 'N/A'}</div>`,

                            // Column 6: Details Toggle (only for mobile)
                            detailsToggle
                        ]).draw(false);
                    });

                    // ✅ Update total expense
                    const formattedTotal = totalAmount.toFixed(2);
                    const totalText = currencyPosition === 'left' ?
                        `${currencySymbol}${formattedTotal}` : `${formattedTotal}${currencySymbol}`;
                    $('#total-expense-amount').text(`Total: ${totalText}`);

                    // ✅ Show filter summary
                    showExpenseFilterSummary(filter, month, year, expenseTypeId);

                    // ✅ Fix top scroll sync
                    const topScroll = document.querySelector('.table-scroll-top');
                    const tableResponsive = document.querySelector('.table-responsive');
                    const tableElement = document.querySelector('.datanew');

                    if (topScroll && tableResponsive && tableElement) {
                        const topInnerDiv = topScroll.querySelector('div');
                        topInnerDiv.style.width = tableElement.scrollWidth + 'px';

                        topScroll.onscroll = () => {
                            tableResponsive.scrollLeft = topScroll.scrollLeft;
                        };
                        tableResponsive.onscroll = () => {
                            topScroll.scrollLeft = tableResponsive.scrollLeft;
                        };
                    }
                },
                error: function() {
                    alert('Failed to fetch expenses.');
                }
            });
        }

        // 🟢 Global array to track selected expense IDs across all pages
        let allSelectedIds = [];

        /** -------------------------------------------------------
         * ✅ Handle "Select All" checkbox across all DataTable pages
         * ------------------------------------------------------- */
        $(document).on('change', '#select-all', function() {
            const isChecked = $(this).is(':checked');

            // ✅ Check or uncheck all checkboxes across all pages
            table.$('input[name="item_ids[]"]').prop('checked', isChecked);

            if (isChecked) {
                // Add all expense IDs from all pages
                allSelectedIds = table.$('input[name="item_ids[]"]').map(function() {
                    return $(this).val();
                }).get();
            } else {
                // Clear all selections
                allSelectedIds = [];
            }

            console.log("✅ All Selected IDs (All Pages):", allSelectedIds);
        });

        /** -------------------------------------------------------
         * 🟢 Handle individual checkbox selection (per row)
         * ------------------------------------------------------- */
        $(document).on('change', 'input[name="item_ids[]"]', function() {
            const id = $(this).val();

            if ($(this).is(':checked')) {
                if (!allSelectedIds.includes(id)) allSelectedIds.push(id);
            } else {
                allSelectedIds = allSelectedIds.filter(x => x !== id);
            }

            // ✅ Update Select-All checkbox state based on all pages
            const totalCheckboxes = table.$('input[name="item_ids[]"]').length;
            const checkedCheckboxes = table.$('input[name="item_ids[]"]:checked').length;
            $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);

            console.log("🟢 Currently Selected IDs (All Pages):", allSelectedIds);
        });

        // Toggle details icon
        $(document).on('click', '.toggle-details', function() {
            let icon = $(this).find('i');
            if (icon.hasClass('fa-plus-circle')) {
                icon.removeClass('fa-plus-circle')
                    .addClass('fa-minus-circle')
                    .css('color', 'red');
            } else {
                icon.removeClass('fa-minus-circle')
                    .addClass('fa-plus-circle')
                    .css('color', '#ff9f43');
            }
        });

        function showExpenseFilterSummary(filter, month, year, expenseTypeId) {
            let message = "All Expenses";

            if (filter) {
                const filterLabels = {
                    'this_week': 'This Week',
                    'this_month': 'This Month',
                    'last_6_months': 'Last 6 Months',
                    'this_year': 'This Year',
                    'previous_year': 'Previous Year'
                };
                message = `Expenses - ${filterLabels[filter] || 'Custom'}`;
            }

            // ✅ Handle month/year logic cleanly
            if (month && year) {
                const monthName = new Date(year, month - 1).toLocaleString('default', {
                    month: 'long'
                });
                message = `Expenses for ${monthName} ${year}`;
            } else if (month && !year) {
                const monthName = new Date(2025, month - 1).toLocaleString('default', {
                    month: 'long'
                }); // fallback year
                message = `Expenses for ${monthName}`;
            } else if (year && !month) {
                message = `Expenses for ${year}`;
            }

            if (expenseTypeId) {
                const selectedText = $('#expenseType-filter option:selected').text();
                message += ` — ${selectedText}`;
            }

            const $summary = $('#expense-filter-summary');
            $summary
                .css('display', 'flex') // ✅ ensures flexbox layout
                .hide()
                .text(message)
                .fadeIn(200);
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            const options = {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            };
            return date.toLocaleDateString('en-GB', options);
        }

        /** --------------------------------------
         * 📊 Expense Chart Function
         * -------------------------------------- */
        let expenseChart;

        function loadExpenseChart(filter = '', month = '', year = '', expenseTypeId = '') {
            $.ajax({
                url: "{{ url('/api/expenses/report') }}",
                method: 'GET',
                data: {
                    filter,
                    month,
                    year,
                    expense_type_id: expenseTypeId,
                    selectedSubAdminId,
                },
                headers: {
                    "Authorization": "Bearer " + authToken
                },
                success: function(response) {
                    const chartContainer = $("#expenseChart").parent();
                    $("#noExpenseMessage").remove();

                    // Destroy previous chart if exists
                    if (expenseChart) {
                        expenseChart.destroy();
                        expenseChart = null;
                    }

                    if (!response.data || response.data.length === 0) {
                        // ✅ Show no-data message
                        // chartContainer.append(`
                        //         <div id="noExpenseMessage"
                        //             style="
                        //                 display: flex;
                        //                 align-items: center;
                        //                 justify-content: center;
                        //                 height: 100%;
                        //                 width: 100%;
                        //                 color: #6b7280;
                        //                 font-size: 16px;
                        //                 text-align: center;
                        //             ">
                        //             No expense data found.
                        //         </div>
                        //     `);
                        chartContainer.append(`
                            <div id="noExpenseMessage">
                                No expense data found.
                            </div>
                        `);
                        return;
                    }

                    // ✅ Aggregate expenses by name
                    const totals = {};
                    response.data.forEach(item => {
                        const name = item.expense_name || 'Unknown';
                        const amount = parseFloat(item.amount) || 0;
                        totals[name] = (totals[name] || 0) + amount;
                    });

                    // ✅ Sort and take top 10
                    const sorted = Object.entries(totals)
                        .sort((a, b) => b[1] - a[1])
                        .slice(0, 15);

                    const labels = sorted.map(item => item[0]);
                    const values = sorted.map(item => item[1]);

                    const ctx = document.getElementById('expenseChart').getContext('2d');
                    expenseChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                label: 'Total Expense',
                                data: values,
                                backgroundColor: [
                                    '#4e73df', '#1cc88a', '#36b9cc',
                                    '#f6c23e', '#e74a3b', '#858796',
                                    '#5a5c69', '#20c997', '#6610f2', '#fd7e14'
                                ],
                                borderWidth: 1,
                                borderRadius: 4,
                                barThickness: 20
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: context => `₹${context.formattedValue}`
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        autoSkip: false,
                                        callback: function(value) {
                                            const label = this.getLabelForValue(value);
                                            return label.length > 15 ? label.substring(
                                                0, 15) + "..." : label;
                                        }
                                    },
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: "#e5e7eb"
                                    }
                                }
                            }
                        }
                    });
                },
                error: function(xhr) {
                    console.error("Expense chart load failed", xhr);
                }
            });
        }

        document.getElementById('generate-pdf').addEventListener('click', function() {
            if (allSelectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one expense to generate the PDF.',
                    confirmButtonColor: '#ff9f43',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const url = `/expense/report/${allSelectedIds.join(',')}`;
            window.open(url, '_blank');
        });

        // Auto-hide error message after 4 seconds
        setTimeout(function() {
            let alert = document.getElementById('error-message');
            if (alert) {
                alert.classList.add('hidden');
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            }
        }, 4000);
    });
</script>
@endpush