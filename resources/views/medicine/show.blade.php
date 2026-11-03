@extends('layout.app')
<style>
    .card-footer {
        background-color: #87ceb0 !important;
    }

    .userProfile {
        width: 180px;
        height: 180px;
        object-fit: cover;
        border-radius: 20px;
        border: 2px solid #fed9cf;
    }

    .card-header {
        background-color: #f89884 !important;
    }

    .btn-rounded {
        background-color: #fed9cf !important;
    }

    .nav-pills .nav-link.active {
        background-color: #f89884 !important;
        color: #ffffff !important;
    }

    .nav-pills .nav-link {
        transition: all 0.2s ease-in-out;
    }

    @media screen and (max-width:767px) {
        .page-title {
            font-size: 18px !important;
        }

        .table-responsive-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive-wrapper table {
            width: 700px;
            min-width: 100%;
            display: block;
        }
    }

    #documentation-section table th, #documentation-section table td { text-align: center !important; vertical-align: middle !important; }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content" style="min-height:100vh">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-8 col-8"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right" style="background-color: #f89884 !important;">
                            <h3 style="float:left" class="text-white">
                                <i class="fa fa-info-circle icon-style2"></i>
                                <span>Medicine Details</span>
                            </h3>
                            <div class="d-flex text-right" style="justify-content: end;">
                                @if (app('hasPermission')(4, 'view'))
                                    <div class="col-sm-4 col-4 text-right m-b-2">
                                        <a href="{{ route('medicine.index') }}" class="btn btn-primary btn-rounded btn-hdr" style="color: #333; font-weight: 600;">
                                            <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span></a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body mt-3">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-5 col-12 text-center margin-img mb-3">
                                            <p class="text-dark">
                                                <img id="medicine_image" class="userProfile" src="{{ asset(env('IMAGE_PATH') . 'admin/assets/img/default.webp') }}"
                                                    alt="Medicine Image">
                                            </p>
                                        </div>
                                        <div class="col-lg-7 col-12">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fa fa-id-badge icon-style1"></i> Medicine: </strong>
                                                <span id="medicine_name" class="fw-bold" style="font-size: 16px;"></span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fa fa-cubes icon-style1"></i> Quantity: </strong>
                                                <span id="medicine_quantity" style="border: 1px solid #ececec; padding: 2px 8px; border-radius: 4px; display: inline-block; font-weight: 600;"></span>
                                                <span id="medicine_low_stock_badge" class="badge ms-2" style="background-color: #ea5455; color: #fff; font-size: 11px; padding: 3px 8px; border-radius: 4px; display: none;">Low Stock</span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-tag icon-style1"></i> Medicine Unit: </strong>
                                                <span id="medicine_unit_display"></span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fa fa-align-left icon-style1"></i> Category: </strong>
                                                <span id="medicine_category"></span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-money-bill-wave icon-style1"></i> Price (MRP): </strong>
                                                <span id="medicine_unit" class="fw-bold text-success"></span>
                                            </p>
                                            <hr class="margin-text">

                                            @php
                                                $gstEnabled = \App\Models\Setting::getValue('gst_status', 'on') === 'on';
                                            @endphp
                                            @if($gstEnabled)
                                            <p class="text-dark margin-text">
                                                <strong><i class="fa fa-percent icon-style1"></i> GST Option: </strong>
                                                <span id="medicine_gst_option"></span>
                                            </p>
                                            <hr class="margin-text" id="gst_option_hr">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fa fa-check-circle icon-style1"></i> Status: </strong>
                                        <span id="medicine_status"></span>
                                    </p>
                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fa fa-align-left icon-style1"></i> Description: </strong>
                                        <span id="medicine_description"></span>
                                    </p>
                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fa fa-calendar-plus icon-style1"></i> Manufacture Date: </strong>
                                        <span id="medicine_manufacture_date"></span>
                                    </p>
                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fa fa-barcode icon-style1"></i> Batch Number: </strong>
                                        <span id="medicine_batch_no"></span>
                                    </p>
                                    <hr class="margin-text">

                                    @if($gstEnabled)
                                    <div id="product_gst_div" style="display: none;">
                                        <p class="text-dark margin-text">
                                            <strong><i class="fa fa-file-invoice-dollar icon-style1"></i> Medicine GST: </strong>
                                            <span id="medicine_product_gst"></span>
                                        </p>
                                        <hr class="margin-text">
                                    </div>
                                    @endif

                                    <p class="text-dark margin-text">
                                        <strong><i class="fa fa-calendar-check icon-style1"></i> Expiry Date: </strong>
                                        <span id="medicine_expiry_date"></span>
                                        <span id="medicine_expiry_badge" class="badge ms-2" style="font-size: 11px; padding: 3px 8px; border-radius: 4px; display: none;"></span>
                                    </p>
                                    <hr class="margin-text">
                                </div>
                            </div>

                            <!-- Product Documentation Section -->
                            <div id="documentation-section" class="mt-4 mb-4" style="display:none;">
                                <div class="card" style="border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: none;">
                                    <div class="card-header bg-white py-3" style="border-bottom: 1px solid #edf2f7; border-top-left-radius: 16px; border-top-right-radius: 16px;">
                                        <h5 class="mb-0 fw-bold" style="font-size: 16px; color: #334155;">
                                            <i class="fas fa-file-medical text-success me-2" style="color: #f89884 !important;"></i> Medicine Documentation
                                        </h5>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0" style="border-radius: 12px; overflow: hidden; text-align: center; vertical-align: middle;">
                                                <thead style="background-color: #cfece0; color: #1e293b;">
                                                    <tr>
                                                        <th class="text-center align-middle" style="font-size: 13px; font-weight: 600; text-align: center !important; vertical-align: middle !important;">Batch Number</th>
                                                        <th class="text-center align-middle" style="font-size: 13px; font-weight: 600; text-align: center !important; vertical-align: middle !important;">Manufacturing Date</th>
                                                        <th class="text-center align-middle" style="font-size: 13px; font-weight: 600; text-align: center !important; vertical-align: middle !important;">Expiry Date</th>
                                                        <th class="text-center align-middle" style="font-size: 13px; font-weight: 600; text-align: center !important; vertical-align: middle !important;">COA Documents</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="documentation-body"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                @if (app('hasPermission')(4, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-medicine-btn"
                                        style="color:black; margin-right:10px; font-weight:600;">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if (app('hasPermission')(4, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-medicine"
                                        data-id="{{ $medicine_id }}" style="font-weight:600;">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Stat Cards -->
            <div class="row mt-2">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card p-3 shadow-sm border-0" style="border-radius: 12px; background: #fff5f2;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">Current Stock</span>
                                <h3 class="mb-0 fw-bold mt-1" id="stat_current_stock" style="color: #f89884;">0</h3>
                            </div>
                            <div style="width: 44px; height: 44px; background: #fed9cf; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-cubes" style="color: #f89884; font-size: 20px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card p-3 shadow-sm border-0" style="border-radius: 12px; background: #f0fdf4;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">Total Sold (Patients)</span>
                                <h3 class="mb-0 fw-bold mt-1 text-success" id="stat_total_sold">0</h3>
                            </div>
                            <div style="width: 44px; height: 44px; background: #cfece0; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-shopping-cart" style="color: #10b981; font-size: 20px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card p-3 shadow-sm border-0" style="border-radius: 12px; background: #f0f9ff;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">Total Purchased </span>
                                <h3 class="mb-0 fw-bold mt-1" id="stat_total_purchased" style="color: #0284c7;">0</h3>
                            </div>
                            <div style="width: 44px; height: 44px; background: #e0f2fe; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-truck" style="color: #0284c7; font-size: 20px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card p-3 shadow-sm border-0" style="border-radius: 12px; background: #faf5ff;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">Selling Price (MRP)</span>
                                <h3 class="mb-0 fw-bold mt-1" id="stat_price" style="color: #9333ea;">₹0.00</h3>
                            </div>
                            <div style="width: 44px; height: 44px; background: #f3e8ff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-tag" style="color: #9333ea; font-size: 20px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- History Card -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right" style="background-color:#87ceb0 !important;">
                            <h3 style="float:left" class="text-white">
                                <i class="fa fa-history icon-style2 text-white"></i>
                                <span>Medicine's History</span>
                            </h3>
                        </div>

                        <div class="card-body mt-3">
                            <!-- Nav Pills / Tabs -->
                            <ul class="nav nav-pills mb-3" id="historyTabs" role="tablist" style="gap: 10px;">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="patient-sales-tab" data-bs-toggle="pill" data-bs-target="#patientSalesTabPane" type="button" role="tab" style="border-radius: 50px; font-weight: 600; font-size: 13px; padding: 8px 18px;">
                                        <i class="fa fa-user-injured me-1"></i> Patient Sales & Dispensing (<span id="patient_sales_count">0</span>)
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="vendor-purchases-tab" data-bs-toggle="pill" data-bs-target="#vendorPurchasesTabPane" type="button" role="tab" style="border-radius: 50px; font-weight: 600; font-size: 13px; padding: 8px 18px; background-color: #f1f5f9; color: #475569;">
                                        <i class="fa fa-truck me-1"></i> Vendor Purchases / Stock In (<span id="vendor_purchases_count">0</span>)
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="historyTabContent">
                                <!-- Patient Sales & Dispensing Tab -->
                                <div class="tab-pane fade show active" id="patientSalesTabPane" role="tabpanel">
                                    <div class="table-responsive-wrapper">
                                        <table class="table table-bordered table-striped" id="medicinehistory" style="width: 100%;">
                                            <thead style="background-color: #f89884; color: white;">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Date</th>
                                                    <th>Type / Ref</th>
                                                    <th>Patient Name</th>
                                                    <th>Phone</th>
                                                    <th>Batch No</th>
                                                    <th>Qty Dispensed</th>
                                                    <th>Price (₹)</th>
                                                    <th>Total (₹)</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="medicineRecords"></tbody>
                                        </table>
                                    </div>
                                    <div id="no-medicine-message" class="alert alert-info text-center mt-3" style="display: none; border-radius: 10px;">
                                        <i class="fa fa-info-circle me-1"></i> No patient sales or dispensing history found for this medicine.
                                    </div>
                                </div>

                                <!-- Vendor Purchases Tab -->
                                <div class="tab-pane fade" id="vendorPurchasesTabPane" role="tabpanel">
                                    <div class="table-responsive-wrapper">
                                        <table class="table table-bordered table-striped" id="purchasehistory" style="width: 100%;">
                                            <thead style="background-color: #87ceb0; color: white;">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Date</th>
                                                    <th>Bill No</th>
                                                    <th>Vendor / Supplier</th>
                                                    <th>Batch No</th>
                                                    <th>Qty In</th>
                                                    <th>Free Qty</th>
                                                    <th>Total Qty</th>
                                                    <th>Rate (₹)</th>
                                                    <th>Total (₹)</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="purchaseRecords"></tbody>
                                        </table>
                                    </div>
                                    <div id="no-purchase-message" class="alert alert-info text-center mt-3" style="display: none; border-radius: 10px;">
                                        <i class="fa fa-info-circle me-1"></i> No vendor purchase records found for this medicine.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            var pathParts = window.location.pathname.split('/');
            var medicineId = pathParts[pathParts.length - 1];

            // Tab switching helper
            $('#patient-sales-tab').click(function() {
                $(this).addClass('active').css({'background-color': '#f89884', 'color': '#ffffff'});
                $('#vendor-purchases-tab').removeClass('active').css({'background-color': '#f1f5f9', 'color': '#475569'});
                $('#patientSalesTabPane').addClass('show active');
                $('#vendorPurchasesTabPane').removeClass('show active');
            });

            $('#vendor-purchases-tab').click(function() {
                $(this).addClass('active').css({'background-color': '#87ceb0', 'color': '#ffffff'});
                $('#patient-sales-tab').removeClass('active').css({'background-color': '#f1f5f9', 'color': '#475569'});
                $('#vendorPurchasesTabPane').addClass('show active');
                $('#patientSalesTabPane').removeClass('show active');
            });

            // Fetch Medicine Info + History
            $.ajax({
                url: '/api/medicines/' + medicineId,
                type: 'GET',
                success: function(data) {
                    if (!data.status && !data.medicine) {
                        Swal.fire('Error', 'Medicine details not found.', 'error');
                        return;
                    }

                    const med = data.medicine;
                    const summary = data.summary || {};

                    // Safe ucfirst function
                    function ucfirst(str) {
                        str = typeof str === 'string' ? str : '';
                        return str.charAt(0).toUpperCase() + str.slice(1);
                    }

                    // Render Product Documentation if exists
                    if (med.documentations && med.documentations.length > 0) {
                        $("#documentation-section").show();
                        let docHtml = "";
                        med.documentations.forEach(function(doc) {
                            let coaLinks = "N/A";
                            if (doc.coa_pdfs && doc.coa_pdfs.length > 0) {
                                coaLinks = doc.coa_pdfs.map(function(pdf) {
                                    let pdfUrl = pdf.startsWith("http") ? pdf : `/${pdf}`.replace(/\/+/g, "/");
                                    return `<a href="${pdfUrl}" target="_blank" class="btn btn-sm me-1 mb-1" style="background:#cfece0; color:#1e293b; border-radius:50px; font-weight:600; font-size:12px;"><i class="fas fa-file-pdf text-danger me-1"></i>View COA</a>`;
                                }).join("");
                            }
                            let mfg = doc.mfg_date ? doc.mfg_date.substring(0, 10) : "N/A";
                            let exp = doc.expiry_date ? doc.expiry_date.substring(0, 10) : (med.expiry_date || "N/A");
                            docHtml += `<tr>
                                <td class="text-center align-middle fw-bold" style="text-align: center !important; vertical-align: middle !important;">${doc.batch_number || med.batch_no || "N/A"}</td>
                                <td class="text-center align-middle" style="text-align: center !important; vertical-align: middle !important;">${mfg}</td>
                                <td class="text-center align-middle" style="text-align: center !important; vertical-align: middle !important;">${exp}</td>
                                <td class="text-center align-middle" style="text-align: center !important; vertical-align: middle !important;">${coaLinks}</td>
                            </tr>`;
                        });
                        $("#documentation-body").html(docHtml);
                    }

                    // Main Medicine Info
                    $('#medicine_name').text(ucfirst(med.name));
                    $('#medicine_description').text(med.description ? ucfirst(med.description) : 'N/A');
                    let priceVal = parseFloat(med.mrp || med.unit || 0).toFixed(2);
                    $('#medicine_unit').text('₹' + priceVal);
                    $('#stat_price').text('₹' + priceVal);
                    $('#medicine_unit_display').text(med.medicine_unit ?? (med.stock_unit ? med.stock_unit.name : 'N/A'));
                    $('#medicine_status').html(med.status === 'active' 
                        ? '<span class="badge" style="background-color: #cfece0; color: #1e293b; border-radius: 50px; padding: 4px 10px; font-weight: 600;">Active</span>' 
                        : '<span class="badge" style="background-color: #fee2e2; color: #dc2626; border-radius: 50px; padding: 4px 10px; font-weight: 600;">Inactive</span>');
                    
                    let qty = parseInt(med.quantity) || 0;
                    $('#medicine_quantity').text(qty);
                    $('#stat_current_stock').text(qty);

                    // Low Stock Badge
                    let lowThreshold = (med.low_stock_qty !== null && med.low_stock_qty !== undefined && med.low_stock_qty !== '') ? parseInt(med.low_stock_qty) : 10;
                    if (qty <= lowThreshold || summary.is_low_stock) {
                        $('#medicine_low_stock_badge').show();
                    } else {
                        $('#medicine_low_stock_badge').hide();
                    }

                    // Expiry Date & Badge
                    if (med.expiry_date) {
                        let dateObj = new Date(med.expiry_date);
                        let formattedDate = med.expiry_date;
                        if (!isNaN(dateObj.getTime())) {
                            let m = String(dateObj.getMonth() + 1).padStart(2, '0');
                            let y = dateObj.getFullYear();
                            formattedDate = `${m}/${y} (${med.expiry_date})`;
                        }
                        $('#medicine_expiry_date').text(formattedDate);

                        let today = new Date();
                        today.setHours(0, 0, 0, 0);
                        let expiryThreshold = new Date(today);
                        expiryThreshold.setDate(today.getDate() + 60);

                        if (dateObj < today || summary.expiry_status === 'expired') {
                            $('#medicine_expiry_badge').text('Expired').css({'background-color': '#dc2626', 'color': '#ffffff'}).show();
                            $('#medicine_expiry_date').css({'color': '#dc2626', 'font-weight': '600'});
                        } else if (dateObj <= expiryThreshold || summary.expiry_status === 'expiring') {
                            $('#medicine_expiry_badge').text('Expiring').css({'background-color': '#ea5455', 'color': '#ffffff'}).show();
                            $('#medicine_expiry_date').css({'color': '#ea5455', 'font-weight': '600'});
                        }
                    } else {
                        $('#medicine_expiry_date').text('N/A');
                    }

                    $('#medicine_manufacture_date').text(med.manufacture_date || 'N/A');
                    $('#medicine_batch_no').text(med.batch_no || 'N/A');

                    const gstOption = med.gst_option ?? 'Without GST';
                    $('#medicine_gst_option').text(gstOption);

                    function normalizeGstValue(value) {
                        if (value === null || value === undefined || value === '') return [];
                        if (Array.isArray(value)) return value;
                        if (typeof value === 'object') return [value];
                        if (typeof value === 'string') {
                            try {
                                const parsed = JSON.parse(value);
                                if (Array.isArray(parsed)) return parsed;
                                if (typeof parsed === 'object' && parsed !== null) return [parsed];
                            } catch (e) {}
                            return value.split(',').map(v => v.trim()).filter(Boolean);
                        }
                        return [String(value)];
                    }

                    function formatGstDisplay(items, unitValue) {
                        if (!items || items.length === 0) return '';
                        const unitNum = parseFloat(unitValue);
                        return items.map(function(item) {
                            if (typeof item === 'object' && item !== null) {
                                let name = item.tax_name || 'GST';
                                let rate = item.tax_rate || '0';
                                let amount = item.tax_amount || 0;
                                return `${name} (${parseFloat(rate).toFixed(2)}%) = ${parseFloat(amount).toFixed(2)}`;
                            }
                            const text = String(item);
                            const match = text.match(/(\d+(\.\d+)?)%/);
                            if (match && Number.isFinite(unitNum)) {
                                const rate = parseFloat(match[1]);
                                const amount = (unitNum * rate) / 100;
                                return `${text} = ${amount.toFixed(2)}`;
                            }
                            return text;
                        }).join(', ');
                    }

                    if (String(gstOption).toLowerCase() === 'with gst') {
                        const gstItems = normalizeGstValue(med.product_gst);
                        const gstText = formatGstDisplay(gstItems, med.unit) || 'N/A';
                        $('#medicine_product_gst').text(gstText);
                        $('#product_gst_div').show();
                    } else {
                        $('#product_gst_div').hide();
                    }

                    const categoryName = med.category && med.category.name ? med.category.name : 'N/A';
                    $('#medicine_category').text(ucfirst(categoryName));

                    if (med.image && med.image.trim() !== '') {
                        $('#medicine_image').attr('src', med.image).show();
                    }

                    $(".edit-medicine-btn").attr("href", "/medicine/edit/" + med.id);

                    // Summary Stats
                    $('#stat_total_sold').text(summary.total_sold ?? 0);
                    $('#stat_total_purchased').text(summary.total_purchased ?? 0);

                    // 1. Populate Patient Sales & Dispensing History
                    const history = data.history || [];
                    $('#patient_sales_count').text(history.length);

                    if (history.length > 0) {
                        let html = '';
                        history.forEach((row, index) => {
                            let actionBtn = row.view_url 
                                ? `<a href="${row.view_url}" target="_blank" class="btn btn-sm" style="background:#cfece0; color:#1e293b; border-radius:50px; font-weight:600; font-size:11px;"><i class="fa fa-file-invoice text-success me-1"></i>Invoice</a>` 
                                : '<span class="text-muted">-</span>';
                            let sourceBadge = `<span class="badge" style="background:#f1f5f9; color:#475569; font-size:10px; border-radius:4px;">${row.source}</span>`;
                            html += `<tr>
                                <td>${index + 1}</td>
                                <td>${row.date}</td>
                                <td><strong>${row.ref_no}</strong><br>${sourceBadge}</td>
                                <td><strong>${row.patient_name}</strong></td>
                                <td>${row.patient_phone}</td>
                                <td>${row.batch_no}</td>
                                <td><span class="badge bg-light text-dark fw-bold" style="font-size:12px; border:1px solid #ddd;">${row.quantity}</span></td>
                                <td>₹${parseFloat(row.price).toFixed(2)}</td>
                                <td class="fw-bold text-success">₹${parseFloat(row.total_amount).toFixed(2)}</td>
                                <td>${actionBtn}</td>
                            </tr>`;
                        });

                        document.getElementById("medicineRecords").innerHTML = html;
                        $('#medicinehistory').show();
                        $('#no-medicine-message').hide();

                        $('#medicinehistory').DataTable({
                            responsive: true,
                            paging: true,
                            searching: true,
                            ordering: true,
                            destroy: true
                        });
                    } else {
                        $('#medicinehistory').hide();
                        $('#no-medicine-message').show();
                    }

                    // 2. Populate Vendor Purchases History
                    const purchases = data.purchases_history || [];
                    $('#vendor_purchases_count').text(purchases.length);

                    if (purchases.length > 0) {
                        let phtml = '';
                        purchases.forEach((row, index) => {
                            let actionBtn = row.view_url 
                                ? `<a href="${row.view_url}" target="_blank" class="btn btn-sm" style="background:#cfece0; color:#1e293b; border-radius:50px; font-weight:600; font-size:11px;"><i class="fa fa-file-alt text-success me-1"></i>View Bill</a>` 
                                : '<span class="text-muted">-</span>';
                            phtml += `<tr>
                                <td>${index + 1}</td>
                                <td>${row.date}</td>
                                <td><strong>${row.bill_no}</strong></td>
                                <td><strong>${row.vendor_name}</strong></td>
                                <td>${row.batch_no}</td>
                                <td><span class="badge bg-light text-dark fw-bold" style="font-size:12px; border:1px solid #ddd;">${row.quantity}</span></td>
                                <td>${row.free_quantity}</td>
                                <td class="fw-bold">${row.total_quantity}</td>
                                <td>₹${parseFloat(row.rate).toFixed(2)}</td>
                                <td class="fw-bold text-primary">₹${parseFloat(row.total_amount).toFixed(2)}</td>
                                <td>${actionBtn}</td>
                            </tr>`;
                        });

                        document.getElementById("purchaseRecords").innerHTML = phtml;
                        $('#purchasehistory').show();
                        $('#no-purchase-message').hide();

                        $('#purchasehistory').DataTable({
                            responsive: true,
                            paging: true,
                            searching: true,
                            ordering: true,
                            destroy: true
                        });
                    } else {
                        $('#purchasehistory').hide();
                        $('#no-purchase-message').show();
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to fetch Medicine details.', 'error');
                }
            });
        });
    </script>

    <script>
        $(document).on('click', '.delete-medicine', function() {
            var medicineId = $(this).data('id');

            if (!medicineId) {
                Swal.fire('Error', 'Medicine ID not found!', 'error');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#cfece0',
                cancelButtonColor: '#f89884',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/api/medicines/' + medicineId,
                        type: 'DELETE',
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Medicine deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "{{ route('medicine.index') }}";
                            });
                        },
                        error: function(xhr) {
                            if (xhr.status === 409) {
                                Swal.fire('Cannot Delete', xhr.responseJSON.message, 'warning');
                            } else {
                                Swal.fire('Error', 'Please remove medicine from patient medicine records first.', 'error');
                            }
                        }
                    });
                }
            });
        });
    </script>

    <style>
        .icon-style1 {
            background-color: white;
            color: #f89884;
            padding: 5px;
            font-size: 18px;
            border-radius: 50%;
            border: 1px solid #fed9cf;
        }

        .icon-style2 {
            color: white;
            padding: 5px;
            font-size: 20px;
            border-radius: 50%;
        }
    
    #documentation-section table th, #documentation-section table td { text-align: center !important; vertical-align: middle !important; }
</style>
@endsection
