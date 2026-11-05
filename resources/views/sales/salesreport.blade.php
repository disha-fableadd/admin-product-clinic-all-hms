@extends('layout.app')

@section('title', 'Sales Report')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />
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

    table.datanew thead tr th, .datanew thead tr th, table thead tr th {
        background-color: #f89884 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        padding: 12px 14px !important;
        border: none !important;
        vertical-align: middle !important;
    }
    table.datanew thead tr th:first-child, .datanew thead tr th:first-child {
        border-top-left-radius: 12px !important;
    }
    table.datanew thead tr th:last-child, .datanew thead tr th:last-child {
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

    /* Mobile toggle button */
    .mobile-toggle-btn-table {
        background: #f89884 !important;
    }
    .mobile-toggle-btn-table:hover {
        background: #f67e65 !important;
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
        .dataTables_info {
            float: left;
            padding-right: 15px;
            font-size: 12px;
            color: #5e5873;
            font-weight: 600;
        }

        tbody tr td:nth-child(2) {
            display: flex;
            align-items: center;
        }

        #DataTables_Table_0_info {
            float: left;
        }

        .table-scroll-top {
            display: none;
        }

        .table-scroll-top div {
            height: 1px;
        }

        /* Dropdown fixes for GST menu: closed by default, vertical list, proper alignment */
        .wordset .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            left: auto;
            flex-direction: column;
            z-index: 2000;
        }

        .wordset .dropdown-menu.show {
            display: block;
        }

        .wordset .dropdown-menu li {
            display: block;
            float: none;
        }

        .wordset .dropdown-menu .dropdown-item {
            display: block;
            /* white-space: nowrap; */
        }

        .total_expense {
            font-weight: 600;
            color: #1b2850;
            border: 1px solid #1b2850;
        }

        /* Mobile toggle button styles */
        .mobile-toggle-btn-table {
            background: #ff9f43;
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            min-width: 32px;
            min-height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s ease;
            padding: 0;
            margin: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .mobile-toggle-btn-table:hover {
            background: #ff8c2e;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .mobile-toggle-btn-table.minus {
            background: #dc3545;
        }

        .mobile-toggle-btn-table.minus:hover {
            background: #c82333;
        }

        /* Ensure Details column is centered */
        .datanew th.text-center,
        .datanew td.text-center {
            text-align: center !important;
        }

        .order-details-row {
            display: none;
        }

        .order-details-row.show {
            display: table-row;
        }

        .order-details-content {
            padding: 15px;
            background: #f8f9fa;
            border-top: 2px solid #ff9f43;
        }

        .order-details-content .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .order-details-content .detail-item:last-child {
            border-bottom: none;
        }

        .order-details-content .detail-label {
            font-weight: 600;
            color: #495057;
        }

        .order-details-content .detail-value {
            color: #212529;
        }

        /* Responsive CSS for table columns */
        @media screen and (max-width: 575.98px) {
            /* Extra small devices - show only checkbox, product name, and details */
            .datanew thead th:nth-child(4),
            .datanew thead th:nth-child(5),
            .datanew thead th:nth-child(6),
            .datanew thead th:nth-child(7),
            .datanew thead th:nth-child(8),
            .datanew tbody td:nth-child(4),
            .datanew tbody td:nth-child(5),
            .datanew tbody td:nth-child(6),
            .datanew tbody td:nth-child(7),
            .datanew tbody td:nth-child(8) {
                display: none;
            }

            .datanew thead th:nth-child(2),
            .datanew tbody td:nth-child(2) {
                width: auto;
            }

            .table-responsive {
                overflow-x: visible !important;
            }
        }

        @media screen and (min-width: 576px) and (max-width: 767.98px) {
            /* Small devices - show checkbox, product name, sold amount, sold qty, and details */
            .datanew thead th:nth-child(4),
            .datanew thead th:nth-child(5),
            .datanew thead th:nth-child(6),
            .datanew tbody td:nth-child(4),
            .datanew tbody td:nth-child(5),
            .datanew tbody td:nth-child(6) {
                display: none;
            }

            .table-responsive {
                overflow-x: visible !important;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 1024px) {
            /* Medium/Tablet - hide details column */
            .datanew thead th:nth-child(3),
            .datanew tbody td:nth-child(3) {
                display: none;
            }

            .table-responsive {
                overflow-x: visible !important;
            }
        }

        @media screen and (min-width: 1025px) {
            /* Large/Desktop - hide details column */
            .datanew thead th:nth-child(3),
            .datanew tbody td:nth-child(3) {
                display: none;
            }
        }

        /* Responsive page header */
        @media screen and (max-width: 767.98px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .total-expense-summary {
                width: 100%;
                margin-top: 10px;
            }

            .total-expense-summary h5 {
                margin: 0 !important;
                width: 100%;
                text-align: center;
            }
        }

        /* Responsive chart card */
        @media screen and (max-width: 767.98px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start !important;
            }

            #sales-filter-summary {
                margin-top: 10px;
                width: 100%;
                white-space: normal !important;
            }

            .card-body {
                /* height: 300px !important; */
                padding: 15px !important;
            }

            #salesProductChart {
                max-height: 280px !important;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 1024px) {
            .card-body {
                height: 320px !important;
            }

            #salesProductChart {
                max-height: 300px !important;
            }
        }

        /* Responsive filters */
        @media screen and (max-width: 767.98px) {
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
        }

        @media screen and (min-width: 576px) and (max-width: 767.98px) {
            .search-set {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }
        }

        @media screen and (max-width: 767px) {
            .table-scroll-top {
                display: none !important;
            }

            .table-responsive {
                overflow-x: visible !important;
                overflow-y: visible !important;
            }

            .datanew {
                width: 100% !important;
                table-layout: auto !important;
            }

            .dataTables_length {
                margin-left: .8rem !important;
                margin-bottom: 1rem !important;
            }

            .dataTables_filter {
                text-align: left !important;
            }
        }
        .table tbody tr td {
            white-space: normal !important;
        }
    
    /* Table top layout & filters */
    .table-top {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
        margin-bottom: 18px !important;
        padding-bottom: 14px !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .table-top .search-set {
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        margin: 0 !important;
        flex: 1 1 auto !important;
    }
    .table-top .search-set select, 
    .table-top .search-set .form-select {
        margin: 0 !important;
        height: 38px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 50px !important;
        padding: 6px 16px !important;
        font-size: 13px !important;
        color: #1e293b !important;
        background-color: #ffffff !important;
        outline: none !important;
        transition: all 0.2s ease !important;
        box-shadow: none !important;
    }
    .table-top .search-set select:focus, 
    .table-top .search-set .form-select:focus {
        border-color: #f89884 !important;
        box-shadow: 0 0 0 3px rgba(248, 152, 132, 0.2) !important;
    }
    .table-top .wordset {
        margin: 0 !important;
        flex-shrink: 0 !important;
    }
    .table-top .wordset ul, .wordset ul {
        list-style: none !important;
        list-style-type: none !important;
        margin: 0 !important;
        padding: 0 !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    .table-top .wordset ul li, .wordset ul li {
        list-style: none !important;
        list-style-type: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .btn-generate-pdf, .wordset .btn, .wordset button, .wordset a button {
        background-color: #f89884 !important;
        border: none !important;
        border-radius: 50px !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        color: #ffffff !important;
        padding: 8px 20px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        box-shadow: 0 2px 8px rgba(248, 152, 132, 0.25) !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
        white-space: nowrap !important;
        text-decoration: none !important;
    }
    .btn-generate-pdf:hover, .wordset .btn:hover, .wordset button:hover, .wordset a button:hover {
        background-color: #f67e65 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(248, 152, 132, 0.35) !important;
        color: #ffffff !important;
    }

    /* DataTables controls styling */
    .dataTables_wrapper .dataTables_length {
        margin-bottom: 12px !important;
        display: inline-flex !important;
        align-items: center !important;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 50px !important;
        padding: 4px 12px !important;
        height: 34px !important;
        outline: none !important;
        margin: 0 6px !important;
    }
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 12px !important;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 50px !important;
        padding: 6px 16px !important;
        height: 36px !important;
        outline: none !important;
    }
    .dataTables_wrapper .dataTables_filter input:focus,
    .dataTables_wrapper .dataTables_length select:focus {
        border-color: #f89884 !important;
        box-shadow: 0 0 0 3px rgba(248, 152, 132, 0.2) !important;
    }

</style>
    <div class="page-wrapper">
    <div class="content px-3 px-md-4 pt-3 pb-4">
        <!-- Main Title / Banner Card matching other clinic pages -->
        <div class="card border-0 mb-4 table-container-card overflow-hidden" style="border-radius: 16px;">
            <div class="card-header" style="background-color: #f89884 !important; border-bottom: none !important; padding: 14px 22px !important; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; border-top-left-radius: 16px !important; border-top-right-radius: 16px !important;">
                <h3 class="card-title d-inline-block text-white mb-0" style="font-weight: 700; font-size: 19px;">
                    <i class="fa fa-chart-line px-2"></i> Sales Report
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
                    Top Sold Items
                </h6>

                <!-- 🔹 Summary inline with title -->
                <div id="sales-filter-summary"
                    style="display:none;
                   background: #f9fafb;
                   border: 1px solid #e5e7eb;
                   border-radius: 6px;
                   padding: 6px 10px;
                   font-size: 14px;
                   font-weight: 500;
                   color: #374151;
                   display: flex;
                   align-items: center;
                   justify-content: center;
                   gap: 6px;
                   white-space: nowrap;
                   transition: all 0.3s ease;">
                </div>
            </div>

            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 260px; position: relative;">
                <canvas id="salesProductChart" style="max-height: 320px; width: 100%;"></canvas>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body" style="padding: 22px;">
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

                        <select id="month-filter" class="form-select form-select-sm">
                            <option value="">All Months</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>

                        <select id="year-filter" class="form-select form-select-sm">
                            <option value="">All Years</option>
                            @for ($y = date('Y'); $y >= date('Y') - 10; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>

                        <select id="vendor-filter" class="form-select form-select-sm" style="min-width: 180px;">
                            <option value="">All Customers / Patients</option>
                            @php
                                $displayList = isset($patients) && count($patients) ? $patients : ($customers ?? []);
                            @endphp
                            @foreach ($displayList as $p)
                                <option value="{{ $p->id }}">{{ $p->fullname ?? $p->name ?? ('Patient #'.$p->id) }}</option>
                            @endforeach
                        </select>

                        <select id="category-filter" class="form-select form-select-sm" style="min-width: 180px;">
                            <option value="">All Categories</option>
                            @isset($categories)
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div class="wordset">
                        <button type="button" class="btn btn-primary btn-sm btn-generate-pdf" id="generate-pdf-btn">
                            <i class="fa-solid fa-file-pdf"></i> View PDF
                        </button>
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
                                <th>Item Name</th>
                                <th class="text-center">Details</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Sold amount</th>
                                <th>Sold qty</th>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("token") || localStorage.getItem("authToken") || @json(session('access_token')) || "";
            const selectedSubAdminId = localStorage.getItem("selectedBranchId") || localStorage.getItem("selectedSubAdminId") || "";
            const ImagePath = "{{ env('ImagePath') ?: (asset('/') . (env('IMAGE_PATH') ?? '')) }}";

            // Global data map for expandable rows
            window.salesReportDataMap = {};

            // 🔹 Initial Load
            fetchOrderReport();
            loadSalesProductChart();

            // 🔹 Trigger filters
            $('#date-filter, #month-filter, #year-filter, #vendor-filter, #category-filter').on('change',
                function() {
                    fetchOrderReport();
                    loadSalesProductChart();
                });

            /** ----------------------------------------------
             * 📱 Mobile Expandable Row Functions
             * ---------------------------------------------- */
            function buildSalesReportExpandableRowContent(item) {
                return `
                    <div class="order-details-content">
                        <div class="detail-item">
                            <span class="detail-label">SKU:</span>
                            <span class="detail-value">${item.SKU || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Category:</span>
                            <span class="detail-value">${item.category || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Brand:</span>
                            <span class="detail-value">${item.brand || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Sold Amount:</span>
                            <span class="detail-value" style="color: #28a745; font-weight: 600;">${item.sold_amount || '₹0.00'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Sold Quantity:</span>
                            <span class="detail-value" style="color: #ff9f43; font-weight: 600;">${item.sold_qty || '0'}</span>
                        </div>
                    </div>
                `;
            }

            window.toggleSalesReportRowDetails = function(productId) {
                // Only work on mobile/tablet (≤1024px)
                if ($(window).width() > 1024) {
                    return;
                }

                const row = $(`tr[data-product-id="${productId}"]`);
                if (row.length === 0) return;

                const detailsRow = row.next(`tr.order-details-row[data-product-details-id="${productId}"]`);
                const toggleBtn = row.find('.mobile-toggle-btn-table');

                if (detailsRow.length === 0) {
                    // Create expandable row if it doesn't exist
                    const item = window.salesReportDataMap[productId];
                    if (!item) return;

                    const expandableContent = buildSalesReportExpandableRowContent(item);
                    const newRow = $(`
                        <tr class="order-details-row show" data-product-details-id="${productId}">
                            <td colspan="8">${expandableContent}</td>
                        </tr>
                    `);
                    row.after(newRow);
                    toggleBtn.addClass('minus').html('-');
                } else {
                    // Toggle existing row
                    if (detailsRow.hasClass('show')) {
                        detailsRow.removeClass('show');
                        toggleBtn.removeClass('minus').html('+');
                    } else {
                        detailsRow.addClass('show');
                        toggleBtn.addClass('minus').html('-');
                    }
                }
            };

            window.addSalesReportExpandableRows = function(dt) {
                // Remove existing expandable rows
                $('.order-details-row').remove();

                // Only add expandable rows on mobile/tablet (≤1024px)
                if ($(window).width() <= 1024) {
                    dt.rows().every(function() {
                        const row = $(this.node());
                        const productId = row.attr('data-product-id');
                        if (productId && window.salesReportDataMap[productId]) {
                            const item = window.salesReportDataMap[productId];
                            const expandableContent = buildSalesReportExpandableRowContent(item);
                            const expandableRow = $(`
                                <tr class="order-details-row" data-product-details-id="${productId}">
                                    <td colspan="8">${expandableContent}</td>
                                </tr>
                            `);
                            row.after(expandableRow);
                        }
                    });
                } else {
                    // On desktop, reset all toggle buttons to +
                    $('.mobile-toggle-btn-table').removeClass('minus').html('+');
                }
            };

            // Resize handler with debouncing
            let resizeTimer;
            window.handleSalesReportResize = function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const currentWidth = $(window).width();

                    // Force browser reflow
                    const body = document.body;
                    body.style.display = 'none';
                    body.offsetHeight; // Trigger reflow
                    body.style.display = '';

                    // Get DataTable instance
                    const table = $('.datanew').DataTable();
                    if (table) {
                        // If switching to desktop (width > 1024px), remove all expandable rows
                        if (currentWidth > 1024) {
                            $('.order-details-row').remove();
                            $('.mobile-toggle-btn-table').removeClass('minus').html('+');
                        }

                        // Adjust columns
                        table.columns.adjust().draw(false);

                        // Re-add expandable rows if needed (only on mobile/tablet)
                        if (currentWidth <= 1024) {
                            setTimeout(function() {
                                window.addSalesReportExpandableRows(table);
                            }, 100);
                        }
                    }

                    // Resize chart if it exists
                    if (window.salesChart) {
                        window.salesChart.resize();
                    }
                }, 250);
            };

            // Add resize event listener
            $(window).on('resize', window.handleSalesReportResize);

            // Add matchMedia listeners for breakpoints
            const mediaQueries = [
                window.matchMedia('(max-width: 575.98px)'),
                window.matchMedia('(min-width: 576px) and (max-width: 767.98px)'),
                window.matchMedia('(min-width: 768px) and (max-width: 1024px)'),
                window.matchMedia('(min-width: 1025px)')
            ];

            mediaQueries.forEach(function(mq) {
                mq.addListener(function() {
                    window.handleSalesReportResize();
                });
            });

            /** ----------------------------------------------
             * 📋 Fetch Order Report Table
             * ---------------------------------------------- */
            function fetchOrderReport() {
                const filters = {
                    filter: $('#date-filter').val(),
                    month: $('#month-filter').val(),
                    year: $('#year-filter').val(),
                    customer_id: $('#vendor-filter').val(),
                    category_id: $('#category-filter').val(),
                    selectedSubAdminId: selectedSubAdminId
                };

                $.ajax({
                    url: "{{ url('/api/order-report') }}",
                    type: "GET",
                    data: filters,
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {

                        if (!response.status) return alert("No orders found.");
                        updateSalesSummary(response);

                        // Clear previous data map
                        window.salesReportDataMap = {};

                        let tbody = "";
                        $.each(response.data, function(index, item) {
                            // Store item data for expandable rows
                            window.salesReportDataMap[item.id] = item;

                            tbody += `
                        <tr data-product-id="${item.id}">
                            <td>
                                <label class="checkboxs">
                                    <input type="checkbox" name="item_ids[]" value="${item.id}">
                                    <span class="checkmarks"></span>
                                </label>
                            </td>
                            <td class="productimgname">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <a href="/product-view/${item.product_id}" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                                        <img src="${item.image_url || (ImagePath + item.image)}" onerror="this.onerror=null;this.src='{{ asset('admin/assets/img/product/noimage.png') }}';" alt="${item.name}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                        <span style="margin-left: 10px;">${item.name}</span>
                                    </a>
                                </div>
                            </td>
                            <td class="text-center">
                                <button class="mobile-toggle-btn-table" onclick="window.toggleSalesReportRowDetails('${item.id}')">+</button>
                            </td>
                            <td>${item.SKU}</td>
                            <td>${item.category}</td>
                            <td>${item.brand ?? 'N/A'}</td>
                            <td>${item.sold_amount}</td>
                            <td>${item.sold_qty}</td>
                        </tr>
                    `;
                        });

                        let table = $('.datanew').DataTable();
                        table.destroy();
                        $(".datanew tbody").html(tbody);
                        let dt = $('.datanew').DataTable({
                            responsive: true
                        });

                        // Add expandable rows after table draws
                        dt.on('draw', function() {
                            window.addSalesReportExpandableRows(dt);
                        });

                        // Initial add of expandable rows
                        setTimeout(function() {
                            window.addSalesReportExpandableRows(dt);
                            // Trigger resize handler to ensure proper layout
                            window.handleSalesReportResize();
                        }, 100);
                    },
                    error: function() {
                        alert("Failed to load order report.");
                    }
                });
            }
            let allSelectedIds = []; // Global array to store selected IDs across pages

            // 🔹 Handle Select-All checkbox
            $(document).on('change', '#select-all', function() {
                const table = $('.datanew').DataTable();
                const isChecked = $(this).is(':checked');

                // ✅ Get all checkboxes across all pages
                table.$('input[name="item_ids[]"]').prop('checked', isChecked);

                if (isChecked) {
                    // Add all IDs from all pages
                    allSelectedIds = table.$('input[name="item_ids[]"]').map(function() {
                        return $(this).val();
                    }).get();
                } else {
                    // Clear all selections
                    allSelectedIds = [];
                }

            });

            // 🔹 Handle individual checkbox changes
            $(document).on('change', 'input[name="item_ids[]"]', function() {
                const table = $('.datanew').DataTable();
                const id = $(this).val();

                if ($(this).is(':checked')) {
                    if (!allSelectedIds.includes(id)) allSelectedIds.push(id);
                } else {
                    allSelectedIds = allSelectedIds.filter(x => x !== id);
                }

                // ✅ Update select-all checkbox based on all pages
                const totalCheckboxes = table.$('input[name="item_ids[]"]').length;
                const checkedCheckboxes = table.$('input[name="item_ids[]"]:checked').length;
                $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);

            });


            /** ----------------------------------------------
             * Load Order Item Chart
             * ---------------------------------------------- */
            function loadSalesProductChart() {
                const filters = {
                    filter: $('#date-filter').val(),
                    month: $('#month-filter').val(),
                    year: $('#year-filter').val(),
                    customer_id: $('#vendor-filter').val(),
                    category_id: $('#category-filter').val(),
                    selectedSubAdminId: selectedSubAdminId
                };

                const chartContainer = $("#salesProductChart").parent();

                $.ajax({
                    url: "{{ url('/api/order-report') }}",
                    type: "GET",
                    data: filters,
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        $("#noSalesMessage").remove();
                        updateSalesSummary(response);
                        if (!response.status || !response.data.length) {
                            if (window.salesChart) window.salesChart.destroy();
                            chartContainer.find('#salesProductChart').hide();
                            chartContainer.append(`
                                <div id="noSalesMessage" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 35px 20px; width: 100%; text-align: center;">
                                    <div style="width: 52px; height: 52px; border-radius: 50%; background: #fff5f2; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                                        <i class="fa fa-chart-pie" style="font-size: 22px; color: #f89884;"></i>
                                    </div>
                                    <div style="font-weight: 600; color: #475569; font-size: 15px;">No Sales Records Found</div>
                                    <p class="text-muted mb-0" style="font-size: 13px;">No sales transactions match the selected filter period.</p>
                                </div>
                            `);
                            return;
                        }
                        chartContainer.find('#salesProductChart').show();

                        // ✅ Aggregate by product
                        const productTotals = {};
                        const productAmounts = {};
                        response.data.forEach(item => {
                            const name = item.name || "Unknown";
                            const qty = parseFloat(item.sold_qty);
                            const amount = parseFloat(item.sold_amount.replace(/[₹,]/g, "")) ||
                                0;
                            productTotals[name] = (productTotals[name] || 0) + qty;
                            productAmounts[name] = (productAmounts[name] || 0) + amount;
                        });

                        const sortedEntries = Object.entries(productTotals)
                            .sort((a, b) => b[1] - a[1])
                            .slice(0, 15);

                        const labels = sortedEntries.map(entry => entry[0]);
                        const values = sortedEntries.map(entry => entry[1]);

                        if (window.salesChart) window.salesChart.destroy();

                        const ctx = document.getElementById("salesProductChart").getContext("2d");
                        window.salesChart = new Chart(ctx, {
                            type: "bar",
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: "Total Quantity Sold",
                                    data: values,
                                    backgroundColor: [
                                        "#4e73df", "#1cc88a", "#36b9cc",
                                        "#f6c23e", "#e74a3b", "#858796",
                                        "#5a5c69", "#20c997", "#6610f2", "#fd7e14"
                                    ],
                                    borderWidth: 1,
                                    borderRadius: 4,
                                    barThickness: 20, // 🔹 Consistent bar width
                                    maxBarThickness: 30
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                resizeDelay: 0,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const label = context.label;
                                                const qty = context.formattedValue;
                                                const amt = productAmounts[label] || 0;
                                                return `${label}: ${qty} pcs | ₹${amt.toFixed(2)}`;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            display: false
                                        }
                                    },
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });

                        // Add resize listener for chart
                        $(window).on('resize', function() {
                            if (window.salesChart) {
                                window.salesChart.resize();
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error("Chart load failed:", xhr);
                    }
                });
            }

            $(document).on('click', '#generate-pdf, #generate-pdf-btn, .btn-generate-pdf', function() {
                if (allSelectedIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Selection',
                        text: 'Please select at least one product to generate the PDF.',
                        confirmButtonColor: '#ff9f43',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                const url = `/sales/report/${allSelectedIds.join(',')}`;
                window.open(url, '_blank');
            });


            function updateSalesSummary(response) {
                const customerText = $('#vendor-filter option:selected').text();
                const categoryText = $('#category-filter option:selected').text();
                const monthText = $('#month-filter option:selected').text();
                const yearText = $('#year-filter option:selected').text();
                const dateText = $('#date-filter option:selected').text();

                const hasCustomer = $('#vendor-filter').val() !== "";
                const hasCategory = $('#category-filter').val() !== "";
                const hasMonth = $('#month-filter').val() !== "";
                const hasYear = $('#year-filter').val() !== "";
                const hasDate = $('#date-filter').val() !== "";

                // ✅ Calculate total sold amount
                let totalAmount = 0;
                response.data.forEach(item => {
                    totalAmount += parseFloat(item.sold_amount.replace(/,/g, '')) || 0;
                });

                // ✅ Update total on top
                $('#total-expense-amount').html(
                    `Total: <span style="color:#ff9f43;">₹${totalAmount.toLocaleString('en-IN', { minimumFractionDigits: 2 })}</span>`
                );

                // Hide summary when no filters selected
                if (!hasCustomer && !hasCategory && !hasMonth && !hasYear && !hasDate) {
                    $('#sales-filter-summary').fadeOut(200);
                    return;
                }

                // Build readable parts
                let parts = [];
                if (hasCustomer) parts.push(`<strong>${customerText}</strong>`);
                if (hasCategory) parts.push(`in <strong>${categoryText}</strong>`);
                if (hasMonth || hasYear) {
                    let timeText = [];
                    if (hasMonth) timeText.push(monthText);
                    if (hasYear) timeText.push(yearText);
                    parts.push(`at <strong>${timeText.join(' ')}</strong>`);
                }
                if (hasDate && dateText !== "All Time") parts.push(`(${dateText})`);

                const message = `Showing sales for ${parts.join(' ')}`;

                // ✅ Show summary beautifully
                $('#sales-filter-summary')
                    .html(`<span>${message}</span>`)
                    .hide()
                    .fadeIn(300);
            }


        });
    </script>
@endpush
