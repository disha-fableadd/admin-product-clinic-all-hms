@extends('layout.app')

@section('title', 'Purchase Report')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

    table.datanew1 thead tr th, .datanew1 thead tr th, table thead tr th {
        background-color: #f89884 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        padding: 12px 14px !important;
        border: none !important;
        vertical-align: middle !important;
    }
    table.datanew1 thead tr th:first-child, .datanew1 thead tr th:first-child {
        border-top-left-radius: 12px !important;
    }
    table.datanew1 thead tr th:last-child, .datanew1 thead tr th:last-child {
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
    .purchase-toggle-btn-table {
        background: #f89884 !important;
    }
    .purchase-toggle-btn-table:hover {
        background: #f67e65 !important;
    }

    /* Hover Rows */
    table.datanew1 tbody tr:hover td {
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
        #productChart {
            width: 100% !important;
            height: 100% !important;
        }

        .total_expense {
            font-weight: 600;
            color: #1b2850;
            border: 1px solid #1b2850;
            border-radius: 6px;
            background: #f8fafc;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .card {
            border-radius: 10px !important;
            overflow: hidden;
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-body {
            background: #ffffff;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: 0.3s ease;
        }

        /* Responsive breakpoints for all screen sizes */

        /* Extra small devices (phones, less than 576px) */
        @media screen and (max-width: 575.98px) {
            /* Page header - stack vertically */
            .page-header {
                flex-direction: column !important;
                align-items: flex-start !important;
            }

            .total-expense-summary {
                width: 100%;
                margin-top: 10px;
            }

            .total_expense {
                width: 90% !important;
                text-align: center;
                font-size: 12px;
                padding: 8px !important;
            }

            /* Chart card header - stack */
            .card-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 10px;
            }

            #sales-filter-summary {
                width: 100%;
                margin-top: 10px;
                font-size: 12px !important;
                white-space: normal !important;
            }

            /* Chart body - adjust height */
            .card-body[style*="height: 360px"] {
                height: 280px !important;
            }

            #productChart {
                max-height: 250px !important;
            }

            /* Filters section - stack vertically */
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
            /* Action buttons - stack */
            .wordset {
                width: 100%;
            }

            .wordset ul {
                flex-direction: column !important;
                gap: 10px !important;
                width: 100%;
            }

            .wordset ul li {
                width: 100%;
                margin: 0 !important;
            }

            .wordset ul li .btn,
            .wordset ul li .dropdown {
                width: 100%;
            }

            .wordset ul li .btn {
                width: 100% !important;
            }

            .wordset ul li a {
                width: 100%;
                display: flex;
                justify-content: center;
                padding: 10px;
            }

            /* GST Reports dropdown - mobile responsive */
            .wordset ul li .dropdown {
                width: 100%;
            }

            .wordset ul li .dropdown .btn {
                width: 100% !important;
                text-align: center;
            }

            .wordset ul li .dropdown-menu {
                width: 100% !important;
                max-width: 100% !important;
            }

            .wordset ul li .dropdown-menu li {
                width: 100%;
            }

            .wordset ul li .dropdown-menu .dropdown-item {
                padding: 12px 15px;
                text-align: center;
                font-size: 14px;
            }

            /* Ensure dropdown opens on mobile */
            @media screen and (max-width: 767.98px) {
                .wordset ul li .dropdown-menu {
                    left: 0 !important;
                    right: 0 !important;
                    transform: translate3d(0, 0, 0) !important;
                    position: absolute !important;
                    margin-top: 5px;
                }
            }

            /* Table responsive styles */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .datanew1 {
                font-size: 11px;
            }

            .datanew1 th,
            .datanew1 td {
                padding: 6px 3px;
            }

            /* Show only Checkbox, Product Name and Details */
            .datanew1 thead th:nth-child(3),
            .datanew1 tbody td:nth-child(3),
            .datanew1 thead th:nth-child(4),
            .datanew1 tbody td:nth-child(4),
            .datanew1 thead th:nth-child(5),
            .datanew1 tbody td:nth-child(5),
            .datanew1 tbody td:nth-child(6),
             .datanew1 thead th:nth-child(6)
            {
                display: none;
            }

            /* Center Details column */
            .datanew1 thead th:nth-child(7),
            .datanew1 tbody td:nth-child(7) {
                text-align: center;
                width: 60px;
                min-width: 60px;
            }

            .purchase-toggle-btn-table {
                margin: 0 auto;
                display: block;
            }
        }

        /* Small devices (landscape phones, 576px and up) */
        @media screen and (min-width: 576px) and (max-width: 767.98px) {
            .page-header {
                flex-direction: row;
                align-items: center;
            }

            .total_expense {
                font-size: 13px;
            }

            .card-header {
                flex-wrap: wrap;
            }

            #sales-filter-summary {
                margin-top: 10px;
                font-size: 13px;
            }

            .card-body[style*="height: 360px"] {
                height: 320px !important;
            }

            .table-top {
                flex-direction: column;
                gap: 12px;
            }

            .search-set {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .search-set .form-select {
                width: 100% !important;
                margin-right: 0 !important;
            }

            .wordset ul {
                flex-wrap: wrap;
                gap: 8px;
            }

            .datanew1 {
                font-size: 12px;
            }

            .datanew1 th,
            .datanew1 td {
                padding: 8px 4px;
            }

            /* Show Checkbox, Product Name, Details, Vendor */
            .datanew1 thead th:nth-child(4),
            .datanew1 tbody td:nth-child(4),
            .datanew1 thead th:nth-child(5),
            .datanew1 tbody td:nth-child(5) {
                display: none;
            }

            /* Center Details column */
            .datanew1 thead th:nth-child(6),
            .datanew1 tbody td:nth-child(6) {
                text-align: center;
                width: 60px;
                min-width: 60px;
            }

            .purchase-toggle-btn-table {
                margin: 0 auto;
                display: block;
            }
        }

        /* Medium devices (tablets, 768px and up to 1024px) */
        @media screen and (min-width: 768px) and (max-width: 1024px) {
            .datanew1 {
                font-size: 13px;
            }
             .purchase-toggle-btn-table {
                display: none !important;
            }
            .datanew1 th,
            .datanew1 td {
                padding: 8px 6px;
            }

            /* Hide Details column on tablets */
            .datanew1 thead th:nth-child(6),
            .datanew1 tbody td:nth-child(6) {
                display: none;
            }

            /* Hide expandable rows on tablets */
            .purchase-report-details-row {
                display: none !important;
            }
        }

        /* Large devices (desktops, 1025px and up) */
        @media screen and (min-width: 1025px) {
            .datanew1 {
                font-size: 14px;
            }

            .datanew1 th,
            .datanew1 td {
                padding: 12px 10px;
            }

            /* Hide Details column on desktop */
            .datanew1 thead th:nth-child(7),
            .datanew1 tbody td:nth-child(7) {
                display: none;
            }

            /* Hide expandable rows on larger screens */
            .purchase-report-details-row {
                display: none !important;
            }
        }

        /* Expandable row details - available for all screen sizes */
        .purchase-report-details-row {
            display: none;
        }

        .purchase-report-details-row.show {
            display: table-row;
        }

        /* Expandable content styles */
        .purchase-report-details-content {
            padding: 15px;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        .purchase-report-details-list {
            margin-bottom: 15px;
        }

        .purchase-report-detail-row-simple {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .purchase-report-detail-row-simple:last-of-type {
            border-bottom: none;
        }

        .purchase-report-detail-label-simple {
            font-weight: 600;
            color: #595b5d;
            font-size: 14px;
        }

        .purchase-report-detail-value-simple {
            color: #1b2850;
            font-size: 14px;
            text-align: right;
        }

        /* Toggle button styles */
        .purchase-toggle-btn-table {
            background: #ff9f43;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .purchase-toggle-btn-table:hover {
            background: #ff8c2e;
        }

        .purchase-toggle-btn-table.minus {
            background: #dc3545;
        }

        .purchase-toggle-btn-table.minus:hover {
            background: #c82333;
        }
    </style>


    <div class="page-wrapper">
    <div class="content px-3 px-md-4 pt-3 pb-4">
        <!-- Main Title / Banner Card matching other clinic pages -->
        <div class="card border-0 mb-4 table-container-card overflow-hidden" style="border-radius: 16px;">
            <div class="card-header" style="background-color: #f89884 !important; border-bottom: none !important; padding: 14px 22px !important; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; border-top-left-radius: 16px !important; border-top-right-radius: 16px !important;">
                <h3 class="card-title d-inline-block text-white mb-0" style="font-weight: 700; font-size: 19px;">
                    <i class="fa fa-shopping-cart px-2"></i> Purchase Report
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

        {{-- <div class="chart-container mt-4"> --}}
        <div class="card shadow-sm border-0 mb-4">
            <!-- 🔹 Header Section -->
            <div
                class="card-header bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap py-3 px-4">
                <div class="d-flex align-items-center">
                    <i class="fa-solid fa-chart-column me-2 text-primary fs-5"></i>
                    <h6 class="fw-bold text-secondary mb-0">Top Purchased Products</h6>
                </div>

                <!-- 🔹 Filter Summary (appears dynamically) -->
                <div id="sales-filter-summary"
                    style="display:none;
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 6px 12px;
                font-size: 14px;
                font-weight: 500;
                color: #374151;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                white-space: nowrap;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                transition: all 0.3s ease;">
                </div>
            </div>

            <!-- 🔹 Chart Body -->
            <div class="card-body d-flex align-items-center justify-content-center" style="height: 360px;">
                <canvas id="productChart" style="max-height: 320px; width: 100%;"></canvas>
            </div>
        </div>
        {{-- </div> --}}
        {{-- ✅ Your Existing Table Section --}}
        <div class="card">
            <div class="card-body">
                <div class="table-top">
                    <div class="search-set">
                        <select id="date-filter" class="form-select form-select-sm" style="margin-right:8px;">
                            <option value="">All Time</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month">This Month</option>
                            <option value="last_6_months">Last 6 Months</option>
                            <option value="this_year">This Year</option>
                            <option value="previous_year">Previous Year</option>
                        </select>

                        <select id="month-filter" class="form-select form-select-sm" style="margin-right:8px;">
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

                        <select id="vendor-filter" class="form-select form-select-sm"
                            style="margin-right:8px; min-width: 180px;">
                            <option value="">All Vendors</option>
                            @isset($vendors)
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            @endisset
                        </select>

                        <select id="category-filter" class="form-select form-select-sm"
                            style="margin-right:8px; min-width: 180px;">
                            <option value="">All Categories</option>
                            @isset($categories)
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div class="wordset">
                        <ul>
                            <li>
                                <button class="btn btn-primary btn-sm" id="generate-pdf">
                                    <i class="fa-solid fa-file-pdf"></i> View PDF
                                </button>
                            </li>
                            {{-- <li class="ms-2">
                            <div class="dropdown">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" id="gstDropdownBtn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    GST Reports
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="gstDropdownBtn">
                                    <li><a class="dropdown-item" href="{{ url('/gst/gstr-3b') }}" target="_blank">GSTR 3B</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/gst/gstr-1') }}" target="_blank">GSTR 1</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/gst/gstr-2') }}" target="_blank">GSTR2 ( purchase )</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/gst/gstr-9c') }}" target="_blank">GSTR 9C</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/gst/gstr-9') }}" target="_blank">GSTR 9</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/tds') }}" target="_blank">TDS</a></li>
                                </ul>
                            </div>
                        </li> --}}

                        {{-- <li>
                            <a id="export-excel" href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Export to CSV">
                                <img src="admin/assets/img/icons/excel.svg" alt="img">
                            </a>
                        </li> --}}
                        </ul>
                    </div>
                </div>

                {{-- ✅ Table --}}
                <div class="table-responsive mt-3">
                    <table class="table datanew1" id="purchase-report-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>Product Name</th>
                                <th>Vendor</th>
                                <th>Purchased Amount</th>
                                <th>Quantity</th>
                                <th>Quantity Type</th>
                                <th class="text-center">Details</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

    {{-- ✅ Scripts --}}
    
    <script>
        // Global variables
        var purchaseReportDataMap = {};

        // Helper function to build expandable row content
        function buildPurchaseReportExpandableRowContent(purchase) {
            return `
                <td colspan="6" class="purchase-report-details-content">
                    <div class="purchase-report-details-list">
                        <div class="purchase-report-detail-row-simple">
                            <span class="purchase-report-detail-label-simple">Vendor:</span>
                            <span class="purchase-report-detail-value-simple">${purchase.vendor ? purchase.vendor.name : 'N/A'}</span>
                        </div>
                        <div class="purchase-report-detail-row-simple">
                            <span class="purchase-report-detail-label-simple">Purchased Amount:</span>
                            <span class="purchase-report-detail-value-simple" style="font-weight: bold; color: #28a745;">₹${parseFloat(purchase.amount_total).toFixed(2)}</span>
                        </div>
                        <div class="purchase-report-detail-row-simple">
                            <span class="purchase-report-detail-label-simple">Quantity:</span>
                            <span class="purchase-report-detail-value-simple">${purchase.quantity || 0}</span>
                        </div>
                        <div class="purchase-report-detail-row-simple">
                            <span class="purchase-report-detail-label-simple">Quantity Type:</span>
                            <span class="purchase-report-detail-value-simple">${purchase.quantity_type || 0}</span>
                        </div>                        
                    </div>
                </td>
            `;
        }

        // Toggle function for purchase report rows
        // window.togglePurchaseReportRowDetails = function(purchaseId) {
        //     const btn = $(`.purchase-toggle-btn-table[data-purchase-id="${purchaseId}"]`);
        //     if (btn.length === 0) return;

        //     const row = btn.closest('tr');
        //     let detailsRow = row.next(`tr.purchase-report-details-row[data-purchase-id="${purchaseId}"]`);
        //     const icon = btn.find('.toggle-icon');

        //     if (detailsRow.length === 0) {
        //         const purchaseData = purchaseReportDataMap[purchaseId];
        //         if (purchaseData) {
        //             detailsRow = $('<tr>')
        //                 .addClass('purchase-report-details-row')
        //                 .attr('data-purchase-id', purchaseId)
        //                 .html(buildPurchaseReportExpandableRowContent(purchaseData));
        //             row.after(detailsRow);
        //         } else {
        //             return;
        //         }
        //     }

        //     if (detailsRow.hasClass('show')) {
        //         detailsRow.removeClass('show');
        //         btn.removeClass('minus');
        //         icon.text('+');
        //     } else {
        //         detailsRow.addClass('show');
        //         btn.addClass('minus');
        //         icon.text('−');
        //     }
        // };
        window.togglePurchaseReportRowDetails = function(purchaseId) {
    const btn = $(`.purchase-toggle-btn-table[data-purchase-id="${purchaseId}"]`);
    const row = btn.closest('tr');
    let detailsRow = row.next(`tr.purchase-report-details-row[data-purchase-id="${purchaseId}"]`);
    const icon = btn.find('.toggle-icon');

    if (detailsRow.length === 0) {
        const purchaseData = purchaseReportDataMap[purchaseId];
        detailsRow = $('<tr>')
            .addClass('purchase-report-details-row')
            .attr('data-purchase-id', purchaseId)
            .html(buildPurchaseReportExpandableRowContent(purchaseData));
        row.after(detailsRow);
    }

    detailsRow.toggleClass('show');
    btn.toggleClass('minus');
    icon.text(detailsRow.hasClass('show') ? '−' : '+');
};


        $(document).ready(function() {
            const authToken = localStorage.getItem("token") || localStorage.getItem("authToken") || @json(session('access_token')) || "";
            const selectedSubAdminId = localStorage.getItem("selectedBranchId") || localStorage.getItem("selectedSubAdminId") || "";
            const ImagePath = "{{ env('ImagePath') ?: (asset('/') . (env('IMAGE_PATH') ?? '')) }}";

            let productChart;

            /** -------------------------------
             * 📦 Load Product-Wise Chart
             * ------------------------------- */
            function loadProductChart(filter = '') {
                $.ajax({
                    url: '{{ url('/api/purchase-product-chart') }}',
                    type: 'GET',
                    data: {
                        filter: filter,
                        month: $('#month-filter').val(),
                        year: $('#year-filter').val(),
                        vendor_id: $('#vendor-filter').val(),
                        category_id: $('#category-filter').val(),
                        selectedSubAdminId: selectedSubAdminId
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        $("#noSalesMessage").remove();

                        const labels = response.labels || [];
                        const values = response.totals || [];
                        const grandTotal = response.grand_total || 0;

                        // ✅ Keep only the latest 15 entries
                        if (labels.length > 15) {
                            const startIndex = labels.length - 15;
                            labels = labels.slice(startIndex);
                            values = values.slice(startIndex);
                        }

                        // ✅ Update total
                        $('#total-expense-amount').html(
                            `Total: <span style="color:#ff9f43;">₹${grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>`
                        );

                        // ✅ Update filter summary
                        updatePurchaseSummary(response);
                        if (!response.status || !response.labels || !response.labels.length) {
                            $("#noSalesMessage").remove();
                            $('#productChart').hide();
                            $('#productChart').parent().append(`
                                <div id="noSalesMessage"
                                    style="
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        height: 300px;
                                        width: 100%;
                                        color: #6b7280;
                                        font-size: 16px;
                                        text-align: center;
                                        background: #f9fafb;
                                        border: 1px solid #e5e7eb;
                                        border-radius: 8px;
                                    ">
                                    No Purchase found.
                                </div>
                            `);
                            return;
                        }

                        if (!labels.length) {
                            $('#productChart').hide();
                            return $('#sales-filter-summary').html(
                                '<span style="color:#e74a3b;">No purchases found.</span>').fadeIn(
                                300);
                        }

                        $('#productChart').show();

                        // Destroy previous chart if exists
                        if (window.productChart && typeof window.productChart.destroy === 'function') {
                            window.productChart.destroy();
                        }

                        const ctx = document.getElementById('productChart').getContext('2d');
                        window.productChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Total Quantity Purchased',
                                    data: values,
                                    backgroundColor: [
                                        '#4e73df', '#1cc88a', '#36b9cc',
                                        '#f6c23e', '#e74a3b', '#858796',
                                        '#5a5c69', '#20c997', '#6610f2', '#fd7e14'
                                    ],
                                    borderWidth: 1,
                                    borderRadius: 6,
                                    barThickness: 20, // 🔹 Consistent bar width
                                    maxBarThickness: 30
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
                                            label: (context) =>
                                                `${context.label}: ${context.formattedValue}`
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        ticks: {
                                            callback: function(value) {
                                                const label = this.getLabelForValue(value);
                                                const words = label.split(' ');
                                                const lines = [];
                                                let line = '';
                                                words.forEach(w => {
                                                    if ((line + w).length < 12)
                                                        line += w + ' ';
                                                    else {
                                                        lines.push(line.trim());
                                                        line = w + ' ';
                                                    }
                                                });
                                                lines.push(line.trim());
                                                return lines;
                                            }
                                        },
                                        grid: {
                                            display: false
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            color: '#e5e7eb'
                                        }
                                    }
                                }
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error('Product chart load failed', xhr);
                    }
                });
            }

            function updatePurchaseSummary(response) {
                const vendorText = $('#vendor-filter option:selected').text() || 'All Vendors';
                const categoryText = $('#category-filter option:selected').text() || 'All Categories';
                const monthText = $('#month-filter option:selected').text() || '';
                const yearText = $('#year-filter option:selected').text() || '';

                const hasVendor = $('#vendor-filter').val() !== "";
                const hasCategory = $('#category-filter').val() !== "";
                const hasMonth = $('#month-filter').val() !== "";
                const hasYear = $('#year-filter').val() !== "";

                if (!hasVendor && !hasCategory && !hasMonth && !hasYear) {
                    $('#sales-filter-summary').hide();
                    return;
                }

                let parts = [];
                if (hasVendor) parts.push(`<strong>${vendorText}</strong>`);
                if (hasCategory) parts.push(`in <strong>${categoryText}</strong>`);
                if (hasMonth || hasYear) {
                    let timeText = [];
                    if (hasMonth) timeText.push(monthText);
                    if (hasYear) timeText.push(yearText);
                    parts.push(`at <strong>${timeText.join(' ')}</strong>`);
                }

                $('#sales-filter-summary')
                    .html(`<span>Showing purchases for ${parts.join(' ')}</span>`)
                    .hide()
                    .fadeIn(300);
            }

            /** -------------------------------
             * 📋 Load Table Data (your existing logic)
             * ------------------------------- */
            // 🔹 Initialize DataTable once globally
            let purchaseTable = $('.datanew1').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                responsive: true,
                language: {
                    emptyTable: "No purchases found.",
                    zeroRecords: "No purchase record found.",
                    search: "Search:",
                    searchPlaceholder: ""
                },
                columns: [{
                        data: 'checkbox',
                        orderable: false
                    },
                    {
                        data: 'product'
                    },
                    {
                        data: 'vendor'
                    },
                    {
                        data: 'amount'
                    },
                    {
                        data: 'quantity'
                    },
                    {
                        data: 'quantity_type'
                    },
                    
                    {
                        data: 'details',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            function loadPurchaseReport(filter = '') {
                $.ajax({
                    url: '{{ url('/api/purchase-report-data') }}',
                    type: 'GET',
                    data: {
                        filter: filter,
                        month: $('#month-filter').val(),
                        year: $('#year-filter').val(),
                        vendor_id: $('#vendor-filter').val(),
                        category_id: $('#category-filter').val(),
                        selectedSubAdminId: selectedSubAdminId
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        // Clear previous data map
                        purchaseReportDataMap = {};

                        // 🔹 Transform API data into DataTable format
                        const data = response.data.map(purchase => {
                            // Store purchase data for expandable row
                            purchaseReportDataMap[purchase.id] = purchase;

                            // Toggle button for Details column
                            const detailsToggle = `
                                <button class="purchase-toggle-btn-table" onclick="togglePurchaseReportRowDetails('${purchase.id}')" data-purchase-id="${purchase.id}">
                                    <span class="toggle-icon">+</span>
                                </button>
                            `;

                            return {
                                checkbox: `<input type="checkbox" name="purchase_ids[]" value="${purchase.id}">`,
                                product: purchase.product ? purchase.product.name : 'N/A',
                                vendor: purchase.vendor ? purchase.vendor.name : 'N/A',
                                amount: `₹${parseFloat(purchase.amount_total).toFixed(2)}`,
                                quantity: purchase.quantity,
                                quantity_type: purchase.quantity_type,
                                details: detailsToggle
                            };
                        });

                        // 🔹 Update DataTable without touching the DOM directly
                        purchaseTable.clear().rows.add(data).draw();

                        // Remove expandable rows if on desktop
                        if ($(window).width() >= 768) {
                            $('.purchase-report-details-row').remove();
                            $('.purchase-toggle-btn-table').removeClass('minus').find('.toggle-icon').text('+');
                        }
                    },
                    error: function(xhr) {
                        console.error("Failed to load purchase report:", xhr);
                    }
                });
            }
            // 🟢 Global array to store selected purchase IDs across pages
            let allSelectedIds = [];

            // 🔹 Handle "Select All" checkbox across all DataTables pages
            $(document).on('change', '#select-all', function() {
                const table = $('.datanew1').DataTable();
                const isChecked = $(this).is(':checked');

                // ✅ Check or uncheck all checkboxes across all pages
                table.$('input[name="purchase_ids[]"]').prop('checked', isChecked);

                if (isChecked) {
                    // Add all purchase IDs from all pages
                    allSelectedIds = table.$('input[name="purchase_ids[]"]').map(function() {
                        return $(this).val();
                    }).get();
                } else {
                    // Clear selections
                    allSelectedIds = [];
                }

                console.log("✅ All Selected IDs (All Pages):", allSelectedIds);
            });

            // 🔹 Handle individual checkbox selection
            $(document).on('change', 'input[name="purchase_ids[]"]', function() {
                const table = $('.datanew1').DataTable();
                const id = $(this).val();

                if ($(this).is(':checked')) {
                    if (!allSelectedIds.includes(id)) allSelectedIds.push(id);
                } else {
                    allSelectedIds = allSelectedIds.filter(x => x !== id);
                }

                // ✅ Update Select-All checkbox state based on all pages
                const totalCheckboxes = table.$('input[name="purchase_ids[]"]').length;
                const checkedCheckboxes = table.$('input[name="purchase_ids[]"]:checked').length;
                $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);

                console.log("🟢 Currently Selected IDs (All Pages):", allSelectedIds);
            });

            /** -------------------------------
             * 🔄 Filters - update chart & table
             * ------------------------------- */
            $('#date-filter, #month-filter, #year-filter, #vendor-filter, #category-filter').on('change',
                function() {
                    const filter = $('#date-filter').val();
                    loadPurchaseReport(filter);
                    loadProductChart(filter);
                });


            // Initial load
            loadPurchaseReport();
            // loadPurchaseChart();
            loadProductChart(); // ✅ Add this line

            // Ensure GST Reports dropdown works on click
            $(document).on('click', '#gstDropdownBtn', function(e) {
                e.stopPropagation();

                const $this = $(this);
                const $dropdown = $this.closest('.dropdown');
                const $menu = $dropdown.find('.dropdown-menu');

                // Check if dropdown is currently open
                const isCurrentlyOpen = $menu.hasClass('show');

                // Close all other dropdowns
                $('.dropdown').not($dropdown).each(function() {
                    $(this).find('.dropdown-menu').removeClass('show');
                    $(this).find('.dropdown-toggle').attr('aria-expanded', 'false');
                });

                // Toggle this dropdown
                if (isCurrentlyOpen) {
                    $menu.removeClass('show');
                    $this.attr('aria-expanded', 'false');
                } else {
                    $menu.addClass('show');
                    $this.attr('aria-expanded', 'true');
                }
            });

            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.dropdown-menu').removeClass('show');
                    $('.dropdown-toggle').attr('aria-expanded', 'false');
                }
            });

            // Prevent dropdown from closing when clicking menu items
            $(document).on('click', '.dropdown-menu', function(e) {
                e.stopPropagation();
            });

            document.getElementById('generate-pdf').addEventListener('click', function() {
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

                const url = `/purchases/report/${allSelectedIds.join(',')}`;
                window.open(url, '_blank');
            });

            // Resize handler for responsive behavior
            let purchaseReportResizeTimer;
            let lastPurchaseReportWidth = $(window).width();

            function forcePurchaseReportCSSRecalculation() {
                const temp = document.createElement('div');
                temp.style.width = '1px';
                temp.style.height = '1px';
                temp.style.position = 'absolute';
                temp.style.visibility = 'hidden';
                document.body.appendChild(temp);
                void temp.offsetWidth;
                void temp.offsetHeight;
                document.body.removeChild(temp);

                void window.innerWidth;
                void window.innerHeight;
                void document.documentElement.offsetWidth;
                void document.documentElement.offsetHeight;
            }

            function handlePurchaseReportResize() {
                clearTimeout(purchaseReportResizeTimer);
                purchaseReportResizeTimer = setTimeout(function() {
                    const currentWidth = $(window).width();
                    lastPurchaseReportWidth = currentWidth;

                    // Remove all expandable rows if on desktop/tablet (>= 768px)
                    if (currentWidth >= 768) {
                        $('.purchase-report-details-row').remove();
                        // Reset all toggle buttons to + state
                        $('.purchase-toggle-btn-table').removeClass('minus').find('.toggle-icon').text('+');
                    }

                    // Force CSS recalculation
                    forcePurchaseReportCSSRecalculation();

                    const purchaseTableEl = document.getElementById('purchase-report-table');
                    const tableResponsive = document.querySelectorAll('.table-responsive');

                    [purchaseTableEl, ...tableResponsive].forEach(function(el) {
                        if (el) {
                            void el.offsetHeight;
                            void el.offsetWidth;
                            el.style.display = 'none';
                            void el.offsetHeight;
                            el.style.display = '';
                        }
                    });

                    // Adjust DataTable columns if table exists
                    if (purchaseTable) {
                        purchaseTable.columns.adjust().draw();
                    }

                    // Resize chart if it exists
                    if (window.productChart && typeof window.productChart.resize === 'function') {
                        window.productChart.resize();
                    }

                    forcePurchaseReportCSSRecalculation();
                }, 50);
            }

            // Window resize handler
            $(window).off('resize.purchaseReport').on('resize.purchaseReport', handlePurchaseReportResize);

            if (window.purchaseReportResizeHandler) {
                window.removeEventListener('resize', window.purchaseReportResizeHandler);
            }
            window.purchaseReportResizeHandler = handlePurchaseReportResize;
            window.addEventListener('resize', window.purchaseReportResizeHandler, { passive: true });

            // Orientation change handler
            $(window).off('orientationchange.purchaseReport').on('orientationchange.purchaseReport', function() {
                setTimeout(function() {
                    lastPurchaseReportWidth = $(window).width();
                    handlePurchaseReportResize();
                }, 300);
            });

            window.addEventListener('orientationchange', function() {
                setTimeout(function() {
                    lastPurchaseReportWidth = $(window).width();
                    handlePurchaseReportResize();
                }, 500);
            });

            // MatchMedia listeners for breakpoint changes
            const purchaseReportQueries = [
                window.matchMedia('(max-width: 575.98px)'),
                window.matchMedia('(min-width: 576px) and (max-width: 767.98px)'),
                window.matchMedia('(min-width: 768px) and (max-width: 1024px)'),
                window.matchMedia('(min-width: 1025px)')
            ];

            purchaseReportQueries.forEach(function(query) {
                if (query.addEventListener) {
                    query.addEventListener('change', function() {
                        setTimeout(handlePurchaseReportResize, 100);
                    });
                } else if (query.addListener) {
                    query.addListener(function() {
                        setTimeout(handlePurchaseReportResize, 100);
                    });
                }
            });

            // Initial width set and call
            lastPurchaseReportWidth = $(window).width();

            $(window).on('load', function() {
                setTimeout(function() {
                    lastPurchaseReportWidth = $(window).width();
                    handlePurchaseReportResize();
                }, 500);
            });

            window.handlePurchaseReportResize = handlePurchaseReportResize;

        });
    </script>
@endsection
