@extends('layout.app')

@section('title', 'Add Sale')

@section('content')
<style>

    #btnGenerateBill {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}
    /* Theme color palette */
    :root {
        --theme-orange: #f89884;
        --theme-orange-hover: #e58774;
        --theme-mint: #cfece0;
        --theme-mint-hover: #bce7d6;
        --theme-dark: #1e293b;
    }

    .single-sale-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        background: #ffffff;
        margin-bottom: 24px;
    }
    .single-sale-card .card-body {
        padding: 24px 28px !important;
    }

    .form-group-compact {
        margin-bottom: 14px !important;
    }
    .form-group-compact label {
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 5px;
        display: block;
        white-space: nowrap;
    }

    .form-control, .form-select,
    .form-control[readonly], .form-control:disabled {
        height: 40px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 50px !important;
        padding: 6px 18px !important;
        font-size: 13.5px !important;
        color: #000000 !important;
        background-color: #ffffff;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #f89884 !important;
        box-shadow: 0 0 0 3px rgba(248, 152, 132, 0.25) !important;
        color: #000000 !important;
        outline: none;
    }
    .form-control::placeholder, .form-select::placeholder, textarea.form-control::placeholder {
        color: #94a3b8 !important;
    }

    /* Select2 Alignment & 50% Radius */
    .select2-container .select2-selection--single {
        height: 40px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 50px !important;
        display: flex !important;
        align-items: center !important;
        padding-left: 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        padding-left: 10px !important;
        color: #000000 !important;
        font-size: 13.5px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        right: 14px !important;
    }

    /* Buttons */
    .btn-submit-main {
        background-color: #f89884 !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 600;
        font-size: 13.5px;
        padding: 10px 26px !important;
        border-radius: 50px !important;
        box-shadow: 0 2px 6px rgba(248, 152, 132, 0.35);
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-submit-main:hover {
        background-color: #e58774 !important;
        color: #ffffff !important;
    }
    .btn-back-main {
        background-color: #cfece0 !important;
        border: 1px solid #b5e4d2 !important;
        color: #000000 !important;
        border-radius: 50px !important;
        font-weight: 600;
        font-size: 13px;
        padding: 7px 20px !important;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    .btn-back-main:hover {
        background-color: #bce7d6 !important;
        color: #000000 !important;
    }
    .btn-add-pill {
        background-color: #f89884 !important;
        color: #ffffff !important;
        border-radius: 50px !important;
        font-size: 11px;
        padding: 2px 10px;
        font-weight: 600;
        float: right;
        cursor: pointer;
        text-decoration: none;
        border: none;
    }

    /* Payment Mode Grid (Pharmacy ERP Style) */
    .pay-mode-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px !important;
        margin-bottom: 12px;
    }
    @media (max-width: 991px) {
        .pay-mode-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 576px) {
        .pay-mode-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    .pay-mode-btn {
        min-height: 52px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px !important;
        background: #ffffff;
        font-size: 13px;
        font-weight: 700;
        color: #1e293b !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 5px;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 8px 12px;
        text-align: center;
    }
    .pay-mode-btn i, .pay-mode-btn svg {
        font-size: 18px;
        color: #f89884;
        transition: color 0.2s ease;
    }
    .pay-mode-btn:hover {
        border-color: #f89884;
        background: #fff8f7;
    }
    .pay-mode-btn.active {
        background-color: #f89884 !important;
        border-color: #f89884 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(248, 152, 132, 0.35);
    }
    .pay-mode-btn.active i, .pay-mode-btn.active svg {
        color: #ffffff !important;
    }

    /* Bottom actions gap */
    .bottom-actions-wrap {
        display: flex;
        gap: 16px !important;
        flex-wrap: wrap;
        margin-top: 14px;
    }

    /* Search input wrap */
    .search-input-wrap {
        position: relative;
    }
    .search-input-wrap .search-icon {
        position: absolute;
        top: 50%;
        left: 18px;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 15px;
        pointer-events: none;
    }
    .search-input-wrap input {
        padding-left: 46px !important;
        height: 44px !important;
    }
    #searchDropdownResults {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        max-height: 280px;
        overflow-y: auto;
        display: none;
    }
    .search-result-item {
        padding: 10px 16px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background 0.15s;
    }
    .search-result-item:hover {
        background: #fbf5f4;
    }

    /* BEAUTIFUL UNIFIED ITEM CARD (One clean, perfectly aligned row) */
    .sale-item-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 14px 14px;
        margin-bottom: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .sale-item-card:hover {
        border-color: #f89884;
        box-shadow: 0 4px 12px rgba(248, 152, 132, 0.12);
    }
    .item-card-thumb {
        width: 52px;
        height: 52px;
        min-width: 52px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        /* margin-right: 14px; */
    }
    .item-card-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
    }
    .item-card-info-col {
        min-width: 170px;
        flex: 2;
        /* margin-right: 14px; */
    }
    .item-card-field {
        /* margin-right: 12px; */
    }
    .item-card-field:last-child {
        margin-right: 0;
    }
    .card-input-label {
        font-size: 10.5px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 4px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .pill-input {
        border-radius: 50px !important;
        height: 38px !important;
        border: 1.5px solid #cbd5e1 !important;
        font-size: 13px !important;
        color: #000000 !important;
        background: #ffffff;
        padding: 6px 14px !important;
        width: 100%;
    }
    .pill-input:focus {
        border-color: #f89884 !important;
        box-shadow: 0 0 0 2px rgba(248, 152, 132, 0.2) !important;
        outline: none;
    }

    /* Stepper Pill */
    .qty-stepper-pill {
        display: inline-flex;
        align-items: center;
        border: 1.5px solid #cbd5e1;
        border-radius: 50px;
        background: #ffffff;
        height: 38px;
        padding: 0 4px;
        width: 100%;
    }
    .stepper-btn {
        width: 28px;
        height: 28px;
        border: none;
        background: transparent;
        color: #f89884;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background 0.15s;
    }
    .stepper-btn:hover {
        background: #fff3f0;
    }
    .stepper-input {
        border: none !important;
        outline: none !important;
        text-align: center;
        font-size: 13.5px;
        font-weight: 700;
        color: #000000;
        width: calc(100% - 56px);
        background: transparent;
    }

    /* Delete Button */
    .btn-card-delete {
        width: 38px;
        height: 38px;
        border: 1px solid #fee2e2;
        background: #fff5f5;
        color: #ef4444;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .btn-card-delete:hover {
        background: #fee2e2;
        color: #dc2626;
    }

    .item-card-total-val {
        font-size: 21px;
        font-weight: 800;
        color: #f89884;
        letter-spacing: -0.5px;
        white-space: nowrap;
    }

    /* MODAL DESIGN */
    #addMedicationModal .modal-content {
        border-radius: 20px;
        overflow: hidden;
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    }
    #addMedicationModal .modal-header {
        background: #ffffff;
        border-bottom: 1.5px solid #f1f5f9;
        padding: 16px 26px;
    }
    #addMedicationModal .modal-body {
        padding: 24px 28px !important;
    }
    #addMedicationModal .form-control,
    #addMedicationModal .form-select,
    #addMedicationModal input,
    #addMedicationModal select {
        height: 40px !important;
        border-radius: 50px !important;
        color: #000000 !important;
        border: 1.5px solid #cbd5e1 !important;
    }
    #addMedicationModal textarea.form-control {
        height: auto !important;
        border-radius: 14px !important;
        color: #000000 !important;
    }
    #addMedicationModal label {
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 5px;
        display: block;
    }
    .btn-close-modal {
        background: transparent !important;
        border: none !important;
        font-size: 20px !important;
        line-height: 1 !important;
        color: #f89884 !important;
        cursor: pointer !important;
        padding: 4px 8px !important;
        transition: transform 0.15s, color 0.15s;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        outline: none !important;
    }
    .btn-close-modal:hover {
        transform: scale(1.18);
        color: #e58774 !important;
    }
    .btn-modal-cancel {
        background-color: #64748b !important;
        color: #ffffff !important;
        border-radius: 50px !important;
        padding: 9px 28px !important;
        font-weight: 600 !important;
        font-size: 13.5px !important;
        border: none !important;
        cursor: pointer;
    }
    .btn-modal-cancel:hover {
        background-color: #475569 !important;
        color: #ffffff !important;
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <!-- Top Toolbar Header -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <h4 class="page-title mb-0" style="font-weight: 700; font-size: 21px; color: #1e293b;">Add Sale</h4>
                
                <div class="d-inline-flex align-items-center gap-2 border-start ps-3" style="margin-left:15px">
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="gst_option" id="radioNoGst" value="without_gst">
                        <label class="form-check-label" for="radioNoGst" style="font-size: 13px; font-weight: 600;">No GST</label>
                    </div>
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="gst_option" id="radioGst" value="with_gst" checked>
                        <label class="form-check-label" for="radioGst" style="font-size: 13px; font-weight: 600;">GST</label>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('sales.list') }}" class="btn-back-main">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <form id="addSaleForm">
            @csrf

            <!-- SINGLE UNIFIED CARD FOR ALL SALE FIELDS -->
            <div class="card single-sale-card">
                <div class="card-body">
                    <!-- Patient Row 1 -->
                    <div class="row g-3 mb-2">
                        <div class="col-lg-3 col-md-4 col-12">
                            <div class="form-group-compact">
                                <label>
                                    <i class="fas fa-user-injured text-muted me-1"></i> Patient Name
                                    <button type="button" class="btn-add-pill" id="quickAddPatientBtn">+ Add</button>
                                </label>
                                <select class="form-control select2" id="patient_id" name="patient_id">
                                    <option value="">Select or Type Patient</option>
                                    @foreach($patients as $p)
                                        <option value="{{ $p->id }}" 
                                            data-phone="{{ $p->phone }}" 
                                            data-age="{{ $p->age }}" 
                                            data-sex="{{ $p->gender }}"
                                            data-bp="{{ $p->bp }}"
                                            data-temp="{{ $p->temperature }}"
                                            data-pulse="{{ $p->pulse }}"
                                            data-weight="{{ $p->weight }}"
                                            data-height="{{ $p->height }}"
                                            data-history="{{ $p->patient_history }}"
                                            data-complaint="{{ $p->patient_complaint }}"
                                            data-lab="{{ $p->advice_for_lab_tests }}">
                                            {{ $p->fullname }} {{ $p->phone ? '('.$p->phone.')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group-compact">
                                <label><i class="fas fa-phone text-muted me-1"></i> Patient Phone</label>
                                <input type="text" class="form-control" name="patient_phone" id="patient_phone" placeholder="Phone number">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-2 col-6">
                            <div class="form-group-compact">
                                <label><i class="fas fa-calendar-alt text-muted me-1"></i> Age</label>
                                <input type="number" class="form-control" name="patient_age" id="patient_age" placeholder="Age" min="0" max="150">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-2 col-6">
                            <div class="form-group-compact">
                                <label><i class="fas fa-venus-mars text-muted me-1"></i> Sex</label>
                                <select class="form-control" name="patient_sex" id="patient_sex">
                                    <option value="Male" selected>Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-1 col-12">
                            <div class="form-group-compact">
                                <label><i class="fas fa-heartbeat text-danger me-1"></i> B.P</label>
                                <input type="text" class="form-control" name="patient_bp" id="patient_bp" placeholder="120/80">
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Vitals & Bill Date -->
                    <div class="row g-3 mb-2">
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="form-group-compact">
                                <label><i class="fas fa-thermometer-half text-warning me-1"></i> Temperature</label>
                                <input type="text" class="form-control" name="patient_temperature" id="patient_temperature" placeholder="e.g. 98.6">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="form-group-compact">
                                <label><i class="fas fa-wave-square text-success me-1"></i> Pulse</label>
                                <input type="text" class="form-control" name="patient_pulse" id="patient_pulse" placeholder="e.g. 72">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-12">
                            <div class="form-group-compact">
                                <label><i class="fas fa-file-medical-alt text-muted me-1"></i> Patient History</label>
                                <input type="text" class="form-control" name="patient_history" id="patient_history" placeholder="Patient history">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-12">
                            <div class="form-group-compact">
                                <label><i class="fas fa-stethoscope text-muted me-1"></i> Patient Complaint</label>
                                <input type="text" class="form-control" name="patient_complaint" id="patient_complaint" placeholder="Patient complaint">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-12">
                            <div class="form-group-compact">
                                <label><i class="fas fa-vial text-muted me-1"></i> Advice for Lab tests</label>
                                <input type="text" class="form-control" name="advice_for_lab_tests" id="advice_for_lab_tests" placeholder="Advice for lab tests">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-12">
                            <div class="form-group-compact">
                                <label><i class="fas fa-calendar-day text-muted me-1"></i> Bill Date</label>
                                <input type="date" class="form-control" name="order_date" id="order_date" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <hr style="margin: 16px 0; border-color: #e2e8f0;">

                    <!-- Total items bar & + Add Product button -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge" style="background-color: #f8fafc; border: 1.5px solid #cbd5e1; color: #1e293b; font-size: 13.5px; font-weight: 700; border-radius: 50px; padding: 7px 18px;" id="itemsCountBadge">Total items : 0</span>
                        <button type="button" class="btn btn-submit-main" id="addProductModalBtn" style="border-radius: 50px; padding: 8px 22px; font-weight: 600; font-size: 13px;">
                            <i class="fas fa-plus me-1"></i> Add Product
                        </button>
                    </div>

                    <!-- Full-Width Search Input -->
                    <div class="search-input-wrap mb-3">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control" id="productSearchInput" placeholder="Search name, category, batch or barcode..." autocomplete="off">
                        <div id="searchDropdownResults"></div>
                    </div>

                    <!-- PRODUCTS LIST (CLEAN UNIFIED CARD FORMAT) -->
                    <div id="saleItemsContainer" class="mb-3">
                        <div id="noItemBox" class="text-center py-5 rounded-4" style="background: #ffffff; border: 1.5px dashed #cbd5e1;">
                            <i class="fas fa-pills fa-3x mb-2 d-block" style="color: #f89884;"></i>
                            <div class="fw-bold" style="color: #64748b; font-size: 14.5px;">Search medicines above or scan barcode to add to bill</div>
                        </div>
                    </div>

                    <!-- Charges Row: Shipping, Misc, Disc %, Disc Rate, Remarks -->
                    <div class="row g-3 align-items-center mb-2">
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="form-group-compact">
                                <label>Shipping Charges</label>
                                <input type="number" step="0.01" class="form-control" name="shipping" id="shipping_charges" value="0">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="form-group-compact">
                                <label>Misc Charges</label>
                                <input type="number" step="0.01" class="form-control" name="misc_charges" id="misc_charges" value="0">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="form-group-compact">
                                <label>Disc % (Overall)</label>
                                <input type="number" step="0.01" class="form-control" name="overall_disc_percent" id="overall_disc_percent" value="0">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="form-group-compact">
                                <label>Disc Rate (₹)</label>
                                <input type="number" step="0.01" class="form-control" name="overall_disc_rate" id="overall_disc_rate" value="0">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-8 col-12">
                            <div class="form-group-compact">
                                <label>Remarks (Optional)</label>
                                <input type="text" class="form-control" name="remarks" id="remarks" placeholder="Enter remarks">
                            </div>
                        </div>
                    </div>

                    <!-- Subtotal & Grand Total Display -->
                    <div class="d-flex justify-content-between align-items-center py-3 px-4 mb-3 rounded-4" style="background: #f8fafc; border: 1.5px solid #e2e8f0;">
                        <div>
                            <span class="text-muted" style="font-size: 13.5px; font-weight: 600;">Subtotal:</span>
                            <strong style="color: #1e293b; font-size: 15px;" class="ms-1" id="displaySubtotal">₹0.00</strong>
                        </div>
                        <div>
                            <span style="font-size: 14.5px; font-weight: 700; color: #1e293b;">Grand Total:</span>
                            <span style="font-size: 20px; font-weight: 800; color: #f89884;" class="ms-1" id="displayGrandTotal">₹0.00</span>
                        </div>
                    </div>

                    <!-- Payment Mode Section (Pharmacy ERP Style) -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-1 mb-2">
                            <i class="far fa-folder-open" style="color: #f89884; font-size: 16px; margin-right:10px"></i>
                            <span style="font-size: 13.5px; font-weight: 700; color: #1e293b;">Payment Mode</span>
                        </div>
                        <div class="pay-mode-container">
                            <div class="pay-mode-btn" data-mode="pay_later">
                                <i class="far fa-clock"></i>
                                <span>Pay Later</span>
                            </div>
                            <div class="pay-mode-btn active" data-mode="cash">
                                <i class="far fa-money-bill-alt"></i>
                                <span>Cash</span>
                            </div>
                            <div class="pay-mode-btn" data-mode="debit">
                                <i class="far fa-credit-card"></i>
                                <span>Debit</span>
                            </div>
                            <div class="pay-mode-btn" data-mode="cash_online">
                                <i class="far fa-money-bill-alt"></i>
                                <span>Cash+Online</span>
                            </div>
                            <div class="pay-mode-btn" data-mode="upi">
                                <i class="fas fa-qrcode"></i>
                                <span>UPI Paid</span>
                            </div>
                        </div>

                        <!-- Cancel Button on the right -->
                        <div class="d-flex justify-content-end mb-2">
                            <button type="button" class="btn btn-sm" id="btnCancelPayment" style="border: 1.5px solid #ef4444; color: #ef4444; border-radius: 8px; font-weight: 600; font-size: 12px; padding: 4px 18px; background: transparent;">
                                Cancel
                            </button>
                        </div>
                        <input type="hidden" name="payment_method" id="payment_method" value="cash">

                        <!-- Payment Details Section -->
                        <div id="paymentDetailsSection" class="p-3 mb-3 rounded-4" style="background: #ffffff; border: 1.5px solid #e2e8f0;">
                            <!-- Paid Type Row -->
                            <div class="row g-3 mb-2" id="paidTypeRow">
                                <div class="col-12">
                                    <div class="form-group-compact">
                                        <label>Paid Type</label>
                                        <select name="paid_type" id="paid_type" class="form-control" style="border-radius: 50px !important;">
                                            <option value="full" selected>Fully</option>
                                            <option value="partial">Partially</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Single Payment Amounts (Cash, Debit, UPI) -->
                            <div class="row g-3 mb-2" id="singleAmountRow">
                                <div class="col-md-6 col-12">
                                    <div class="form-group-compact">
                                        <label>Enter Amount</label>
                                        <input type="number" step="0.01" min="0" class="form-control" name="enter_amount" id="enter_amount" value="0.00" style="border-radius: 50px !important;">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group-compact">
                                        <label>Pending Amount</label>
                                        <input type="text" class="form-control" name="pending_amount" id="pending_amount" value="0.00" readonly style="border-radius: 50px !important; background-color: #f8fafc;">
                                    </div>
                                </div>
                            </div>

                            <!-- Cash + Online Amounts -->
                            <div id="cashOnlineAmountRow" class="d-none">
                                <div class="row g-3 mb-2">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group-compact">
                                            <label>Cash Amount</label>
                                            <input type="number" step="0.01" min="0" class="form-control" name="cash_amount" id="cash_amount" value="0.00" style="border-radius: 50px !important;">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group-compact">
                                            <label>Online Amount</label>
                                            <input type="number" step="0.01" min="0" class="form-control" name="online_amount" id="online_amount" value="0.00" style="border-radius: 50px !important;">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mb-2">
                                    <div class="col-12">
                                        <div class="form-group-compact">
                                            <label>Pending Amount</label>
                                            <input type="text" class="form-control" name="cash_online_pending" id="cash_online_pending" value="0.00" readonly style="border-radius: 50px !important; background-color: #f8fafc;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bank Selection Row (for Debit, UPI, Cash+Online) -->
                            <div class="row g-3 d-none" id="bankSelectionRow">
                                <div class="col-12">
                                    <div class="form-group-compact">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="mb-0">Select Bank</label>
                                            <button type="button" class="btn btn-sm" id="btnOpenAddBankModal" style="background-color: #f89884; color: #ffffff; border-radius: 50px; font-size: 11.5px; padding: 4px 16px; font-weight: 600; border: none;">+ Add Bank</button>
                                        </div>
                                        <select name="bank_id" id="bank_id" class="form-control select2" style="border-radius: 50px !important; width: 100%;">
                                            <option value="">Select Bank</option>
                                            @foreach($banks as $b)
                                                <option value="{{ $b->id }}">{{ $b->bank_name ?? $b->name }} ({{ $b->account_number ?? '' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Action Buttons (Pharmacy ERP Side-by-Side Style) -->
                   <div class="d-flex justify-content-end mt-3">
    <button type="button"
        class="btn btn-submit-main py-2 px-4 d-flex justify-content-between align-items-center gap-3"
        id="btnGenerateBill"
        onclick="submitSale(false)">

        <span>
            Total Amount : <span id="footerTotalAmount">₹0.00</span>
        </span>

        <span class="text-nowrap">
            Generate Bill <i class="fas fa-arrow-right ms-1"></i>
        </span>

    </button>
</div>
                    
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Quick Add Patient -->
<div class="modal fade" id="quickAddPatientModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header d-flex justify-content-between align-items-center" style="background-color: #f89884; color: #ffffff; padding: 16px 24px; border-bottom: none;">
                <h5 class="modal-title font-weight-bold" style="color: #ffffff !important; font-size: 17px; margin: 0; font-weight: 700;"><i class="fas fa-user-plus me-2"></i> Add Patient</h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="color: #ffffff !important; opacity: 0.95 !important; font-size: 26px !important; font-weight: 300 !important; text-shadow: none !important; border: none !important; background: transparent !important; padding: 0 !important; cursor: pointer; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickPatientForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group-compact mb-3">
                        <label>Patient Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="quick_patient_name" id="quick_patient_name" required placeholder="Full Name">
                    </div>
                    <div class="form-group-compact mb-3">
                        <label>Phone Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="quick_patient_phone" id="quick_patient_phone" required placeholder="10-digit mobile">
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="form-group-compact">
                                <label>Age</label>
                                <input type="number" class="form-control" name="quick_patient_age" id="quick_patient_age" placeholder="Age" min="0" max="150">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group-compact">
                                <label>Gender</label>
                                <select class="form-control" name="quick_patient_gender" id="quick_patient_gender">
                                    <option value="Male" selected>Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-white d-flex justify-content-end gap-3" style="border-top: 1.5px solid #f1f5f9;">
                    <button type="button" class="btn btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-submit-main">Save Patient</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Medication (Aligned Grid & Proper Cancel / Close Button) -->
<div class="modal fade" id="addMedicationModal" tabindex="-1" aria-labelledby="addMedicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center" style="background-color: #f89884; color: #ffffff; padding: 16px 24px; border-bottom: none;">
                <h5 class="modal-title font-weight-bold" id="addMedicationModalLabel" style="color: #ffffff !important; font-size: 17px; margin: 0; font-weight: 700;"><i class="fas fa-pills me-2"></i> Add Medication</h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="color: #ffffff !important; opacity: 0.95 !important; font-size: 26px !important; font-weight: 300 !important; text-shadow: none !important; border: none !important; background: transparent !important; padding: 0 !important; cursor: pointer; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addMedicationForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Row 1: Product Type, Category, Medicine Name, Brand -->
                    <div class="row g-3 mb-3">
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>Product Type</label>
                            <select class="form-control" name="service_type" id="modal_service_type">
                                <option value="Medicine" selected>Medicine</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>
                                Category <span class="text-danger">*</span>
                                <button type="button" class="btn-add-pill" id="modalQuickAddCategoryBtn">+ Add Category</button>
                            </label>
                            <select class="form-control select2" name="category_id" id="modal_category_id" required>
                                <option value="">Choose category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>Medicine Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="modal_name" required placeholder="Medicine name">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>
                                Brand
                                <button type="button" class="btn-add-pill" id="modalQuickAddBrandBtn">+ Add Brand</button>
                            </label>
                            <select class="form-control select2" name="brand_id" id="modal_brand_id">
                                <option value="">No brand</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Stock, Stock Unit, GST Option, MRP -->
                    <div class="row g-3 mb-3">
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>Stock <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="quantity" id="modal_quantity" value="0" min="0" required>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>
                                Stock Unit
                                <button type="button" class="btn-add-pill" id="modalQuickAddUnitBtn">+ Add Unit</button>
                            </label>
                            <select class="form-control select2" name="stock_unit_id" id="modal_stock_unit_id">
                                <option value="">Choose Stock Unit</option>
                                @if(isset($stockUnits))
                                    @foreach($stockUnits as $su)
                                        <option value="{{ $su->id }}">{{ $su->name }} ({{ $su->short_name ?? $su->unit_code ?? $su->name }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>GST Option</label>
                            <select class="form-control" name="gst_option" id="modal_gst_option">
                                <option value="Without GST" selected>Without GST</option>
                                <option value="With GST">With GST</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>MRP</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="mrp" id="modal_mrp" placeholder="0.00">
                        </div>
                    </div>

                    <!-- Row 3: Purchase Price, Stock Value, Expiry Date, Pack -->
                    <div class="row g-3 mb-3">
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>Purchase Price</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="purchase_price" id="modal_purchase_price" placeholder="0.00">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>Stock Value</label>
                            <input type="number" step="0.01" class="form-control" name="stock_value" id="modal_stock_value" value="0.00" readonly style="background-color: #f8fafc;">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>Expiry Date</label>
                            <input type="date" class="form-control" name="expiry_date" id="modal_expiry_date">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>Pack</label>
                            <input type="text" class="form-control" name="pack" id="modal_pack" placeholder="e.g. 10 tablets">
                        </div>
                    </div>

                    <!-- Row 4: HSN Code, Barcode, Stock Status -->
                    <div class="row g-3 mb-3">
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>HSN Code</label>
                            <input type="text" class="form-control" name="hsn_code" id="modal_hsn_code" placeholder="Enter HSN">
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label>Barcode</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="text" class="form-control flex-grow-1" name="barcode" id="modal_barcode" placeholder="Enter Barcode">
                                <button type="button" class="btn btn-sm btn-dark" id="modalAddBarcodeBtn" style="border-radius: 50px; padding: 7px 16px; font-weight: 600; flex-shrink: 0;">Add</button>
                                <button type="button" class="btn btn-sm btn-submit-main" id="modalGenBarcodeBtn" style="border-radius: 50px; padding: 7px 16px; font-weight: 600; flex-shrink: 0;">Auto</button>
                                <button type="button" class="btn btn-sm" id="modalScanBarcodeBtn" style="border-radius: 50px; border: 1.5px solid #f89884; color: #f89884; width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>Stock Status</label>
                            <select class="form-control" name="availablility" id="modal_availability">
                                <option value="in_stock" selected>In Stock</option>
                                <option value="out_stock">Out Of Stock</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 5: Status, Product Images, Description -->
                    <div class="row g-3 mb-3">
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>Status</label>
                            <select class="form-control" name="status" id="modal_status">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label>Product Images</label>
                            <input type="file" class="form-control" name="image" id="modal_image" accept="image/*">
                        </div>
                        <div class="col-lg-6 col-md-12 col-12">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="modal_description" rows="1" placeholder="Description"></textarea>
                        </div>
                    </div>

                    <!-- Row 6: Batch Number, Manufacturing Date, COA Documents -->
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6 col-12">
                            <label>Batch Number</label>
                            <input type="text" class="form-control" name="batch_no" id="modal_batch_no" placeholder="Batch number">
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <label>Manufacturing Date</label>
                            <input type="date" class="form-control" name="manufacturing_date" id="modal_mfg_date">
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <label>COA Documents (PDF)</label>
                            <input type="file" class="form-control" name="coa_document" id="modal_coa_doc" accept="application/pdf">
                        </div>
                    </div>
                </div>

                <div class="modal-footer p-3 bg-white d-flex justify-content-end gap-3" style="border-top: 1.5px solid #f1f5f9;">
                    <button type="button" class="btn btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-submit-main" id="saveMedicationBtn">Save Medication</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Quick Add (Category, Brand, Unit) -->
<div class="modal fade" id="quickAddSubModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header d-flex justify-content-between align-items-center" style="background-color: #f89884; color: #ffffff; padding: 16px 24px; border-bottom: none;">
                <h5 class="modal-title font-weight-bold" id="quickAddSubModalTitle" style="color: #ffffff !important; font-size: 17px; margin: 0; font-weight: 700;"><i class="fas fa-plus-circle me-2"></i> Add</h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="color: #ffffff !important; opacity: 0.95 !important; font-size: 26px !important; font-weight: 300 !important; text-shadow: none !important; border: none !important; background: transparent !important; padding: 0 !important; cursor: pointer; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="quickAddSubType">
                <div class="form-group-compact mb-3">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="quickAddSubName" placeholder="Enter name">
                </div>
                <div class="form-group-compact">
                    <label>Description / Short Code</label>
                    <input type="text" class="form-control" id="quickAddSubDesc" placeholder="Enter description or code">
                </div>
                <small class="text-danger error-quick-add-sub mt-2 d-block"></small>
            </div>
            <div class="modal-footer p-3 bg-white d-flex justify-content-end gap-3" style="border-top: 1.5px solid #f1f5f9;">
                <button type="button" class="btn btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-submit-main" id="saveQuickAddSubBtn">Save</button>
            </div>
        </div>
    </div>
</div>

@endsection

<!-- Modal: Add Bank Modal -->
<div class="modal fade" id="addBankModal" tabindex="-1" aria-labelledby="addBankModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header d-flex justify-content-between align-items-center" style="background-color: #f89884; color: #ffffff; padding: 16px 24px; border-bottom: none;">
                <h5 class="modal-title font-weight-bold" id="addBankModalLabel" style="color: #ffffff !important; font-size: 17px; margin: 0; font-weight: 700;"><i class="fas fa-university me-2"></i> Add New Bank</h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="color: #ffffff !important; opacity: 0.95 !important; font-size: 26px !important; font-weight: 300 !important; text-shadow: none !important; border: none !important; background: transparent !important; padding: 0 !important; cursor: pointer; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addBankForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6 col-12">
                            <label class="form-label" style="font-weight: 600; font-size: 12.5px;">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ajax_bank_name" name="bank_name" required placeholder="e.g. State Bank of India" style="border-radius: 50px !important;">
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label" style="font-weight: 600; font-size: 12.5px;">Account Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ajax_account_number" name="account_number" required placeholder="Account Number" style="border-radius: 50px !important;">
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label" style="font-weight: 600; font-size: 12.5px;">IFSC Code</label>
                            <input type="text" class="form-control" id="ajax_ifsc_code" name="ifsc_code" placeholder="IFSC Code" style="border-radius: 50px !important;">
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label" style="font-weight: 600; font-size: 12.5px;">Branch Name</label>
                            <input type="text" class="form-control" id="ajax_branch_name" name="branch_name" placeholder="Branch Name" style="border-radius: 50px !important;">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-weight: 600; font-size: 12.5px;">Opening Balance</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="ajax_opening_balance" name="opening_balance" value="0.00" style="border-radius: 50px !important;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-white d-flex justify-content-end gap-2" style="border-top: 1.5px solid #f1f5f9;">
                    <button type="button" class="btn btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-submit-main" id="saveBankBtn">Save Bank</button>
                </div>
            </form>
        </div>
    </div>
</div>
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    var allMedicines = @json($medicines);
    var allTaxRates = @json($taxes ?? []);
    var selectedItems = [];
    var saleAuthToken = (typeof token !== 'undefined' && token) ? token : (localStorage.getItem('token') || '');
    var saleBranchId = localStorage.getItem('selectedBranchId') || 1;

    $(document).ready(function() {
        if ($('.select2').length) {
            $('.select2').select2({ width: '100%' });
        }

        // Modal Close handlers (works reliably with data-dismiss, data-bs-dismiss and cancel buttons across Bootstrap 4/5)
        $(document).on('click', '[data-dismiss="modal"], [data-bs-dismiss="modal"], .btn-modal-cancel, .btn-close-modal', function(e) {
            e.preventDefault();
            const $m = $(this).closest('.modal');
            const mEl = $m[0];
            if (mEl) {
                try {
                    if (window.bootstrap && bootstrap.Modal) {
                        const bModal = bootstrap.Modal.getInstance(mEl) || new bootstrap.Modal(mEl);
                        bModal.hide();
                    }
                } catch(err) {}
                try {
                    if (typeof $m.modal === 'function') {
                        $m.modal('hide');
                    }
                } catch(err) {}
                $m.removeClass('show').hide();
                $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
                $('.modal-backdrop').remove();
            }
        });

        // Auto calculate stock value in medication modal
        $('#modal_quantity, #modal_purchase_price, #modal_mrp').on('input change', function() {
            const q = parseFloat($('#modal_quantity').val()) || 0;
            const p = parseFloat($('#modal_purchase_price').val()) || parseFloat($('#modal_mrp').val()) || 0;
            $('#modal_stock_value').val((q * p).toFixed(2));
            $('#modal_availability').val(q > 0 ? 'in_stock' : 'out_stock');
        });

        // Barcode Auto Generate in modal
        $('#modalGenBarcodeBtn').on('click', function() {
            const autoCode = 'MED' + Math.floor(100000 + Math.random() * 900000);
            $('#modal_barcode').val(autoCode);
        });

        // Quick add Category/Brand/Unit handlers
        $('#modalQuickAddCategoryBtn').on('click', function() {
            $('#quickAddSubType').val('category');
            $('#quickAddSubModalTitle').text('Add Category');
            $('#quickAddSubName').val('');
            $('#quickAddSubDesc').val('');
            $('.error-quick-add-sub').text('');
            $('#quickAddSubModal').modal('show');
        });

        $('#modalQuickAddBrandBtn').on('click', function() {
            $('#quickAddSubType').val('brand');
            $('#quickAddSubModalTitle').text('Add Brand');
            $('#quickAddSubName').val('');
            $('#quickAddSubDesc').val('');
            $('.error-quick-add-sub').text('');
            $('#quickAddSubModal').modal('show');
        });

        $('#modalQuickAddUnitBtn').on('click', function() {
            $('#quickAddSubType').val('unit');
            $('#quickAddSubModalTitle').text('Add Stock Unit');
            $('#quickAddSubName').val('');
            $('#quickAddSubDesc').val('').attr('placeholder', 'Short code (e.g. TAB, BOX)');
            $('.error-quick-add-sub').text('');
            $('#quickAddSubModal').modal('show');
        });

        $('#saveQuickAddSubBtn').on('click', function() {
            const type = $('#quickAddSubType').val();
            const name = $('#quickAddSubName').val().trim();
            const desc = $('#quickAddSubDesc').val().trim();

            if (!name) {
                $('.error-quick-add-sub').text('Name is required');
                return;
            }

            let endpoint = '';
            let payload = { name: name, branch_id: saleBranchId };

            if (type === 'category') {
                endpoint = '{{ url("api/categories") }}';
                payload.description = desc;
            } else if (type === 'brand') {
                endpoint = '{{ url("api/brands") }}';
                payload.description = desc;
            } else if (type === 'unit') {
                endpoint = '{{ url("api/stock-units") }}';
                payload.short_name = desc;
                payload.status = 'active';
            }

            const $btn = $(this);
            $btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: endpoint,
                type: 'POST',
                headers: { 'Authorization': 'Bearer ' + saleAuthToken, 'Accept': 'application/json' },
                data: payload,
                success: function(res) {
                    $btn.prop('disabled', false).text('Save');
                    $('#quickAddSubModal').modal('hide');

                    if (type === 'category') {
                        const newId = (res.category && res.category.id) ? res.category.id : res.id;
                        const opt = new Option(name, newId, true, true);
                        $('#modal_category_id').append(opt).trigger('change');
                    } else if (type === 'brand') {
                        const newId = (res.brand && res.brand.id) ? res.brand.id : res.id;
                        const opt = new Option(name, newId, true, true);
                        $('#modal_brand_id').append(opt).trigger('change');
                    } else if (type === 'unit') {
                        const newId = (res.stock_unit && res.stock_unit.id) ? res.stock_unit.id : res.id;
                        const opt = new Option(`${name} (${desc || name})`, newId, true, true);
                        $('#modal_stock_unit_id').append(opt).trigger('change');
                    }
                },
                error: function(err) {
                    $btn.prop('disabled', false).text('Save');
                    let msg = 'Error saving item';
                    if (err.responseJSON) {
                        msg = err.responseJSON.message || Object.values(err.responseJSON.errors || {}).flat().join(', ') || msg;
                    }
                    $('.error-quick-add-sub').text(msg);
                }
            });
        });

        // Open Add Medication Modal
        $('#addProductModalBtn').on('click', function() {
            $('#addMedicationForm')[0].reset();
            $('#modal_stock_value').val('0.00');
            $('#addMedicationModal').modal('show');
        });

        // Submit Medication Modal -> AUTO SELECT INTO BILL
        $('#addMedicationForm').on('submit', function(e) {
            e.preventDefault();

            const form = document.getElementById('addMedicationForm');
            const formData = new FormData(form);
            formData.append('branch_id', saleBranchId);

            $('#saveMedicationBtn').prop('disabled', true).text('Saving...');

            $.ajax({
                url: '{{ url("api/medicines") }}',
                type: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + saleAuthToken,
                    'Accept': 'application/json'
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#saveMedicationBtn').prop('disabled', false).text('Save Medication');
                    if (res.status || res.medicine || res.id) {
                        const newMed = res.medicine || res.data || {
                            id: res.id || ('MED_' + Date.now()),
                            name: $('#modal_name').val(),
                            image: res.image || '',
                            batch_no: $('#modal_batch_no').val() || '',
                            expiry_date: $('#modal_expiry_date').val() || '',
                            quantity: parseFloat($('#modal_quantity').val()) || 0,
                            mrp: parseFloat($('#modal_mrp').val()) || 0,
                            purchase_price: parseFloat($('#modal_purchase_price').val()) || 0,
                            gst_option: $('#modal_gst_option').val()
                        };
                        if (res.image && !newMed.image) newMed.image = res.image;

                        allMedicines.unshift(newMed);
                        addItemToSale(newMed);

                        $('#addMedicationModal').modal('hide');
                        form.reset();

                        if (typeof toastr !== 'undefined') {
                            toastr.success('Medicine created and added to sale bill!', 'Success');
                        } else {
                            alert('Medicine created and added to sale bill!');
                        }
                    } else {
                        alert(res.message || 'Failed to save medication');
                    }
                },
                error: function(xhr) {
                    $('#saveMedicationBtn').prop('disabled', false).text('Save Medication');
                    let msg = 'Failed to create medication.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                }
            });
        });

        // Patient selection auto-fetch
        $('#patient_id').on('change', function() {
            const opt = $(this).find(':selected');
            const pId = opt.val();

            if (!pId) {
                $('#patient_phone, #patient_age, #patient_bp, #patient_temperature, #patient_pulse, #patient_history, #patient_complaint, #advice_for_lab_tests').val('');
                $('#patient_sex').val('Male');
                return;
            }

            $('#patient_phone').val(opt.data('phone') || '');
            $('#patient_age').val(opt.data('age') || '');
            $('#patient_sex').val(opt.data('sex') || 'Male');
            $('#patient_bp').val(opt.data('bp') || '');
            $('#patient_temperature').val(opt.data('temp') || '');
            $('#patient_pulse').val(opt.data('pulse') || '');
            $('#patient_history').val(opt.data('history') || '');
            $('#patient_complaint').val(opt.data('complaint') || '');
            $('#advice_for_lab_tests').val(opt.data('lab') || '');

            if (!opt.data('history') && !opt.data('complaint') && !pId.startsWith('NEW_')) {
                $.ajax({
                    url: '/api/patient/' + pId,
                    type: 'GET',
                    headers: saleAuthToken ? { 'Authorization': 'Bearer ' + saleAuthToken } : {},
                    success: function(res) {
                        if (res && res.patient) {
                            const p = res.patient;
                            if (p.phone) $('#patient_phone').val(p.phone);
                            if (p.age) $('#patient_age').val(p.age);
                            if (p.gender) $('#patient_sex').val(p.gender);
                            if (p.bp) $('#patient_bp').val(p.bp);
                            if (p.temperature) $('#patient_temperature').val(p.temperature);
                            if (p.pulse) $('#patient_pulse').val(p.pulse);
                            if (p.patient_history) $('#patient_history').val(p.patient_history);
                            if (p.patient_complaint) $('#patient_complaint').val(p.patient_complaint);
                            if (p.advice_for_lab_tests) $('#advice_for_lab_tests').val(p.advice_for_lab_tests);
                        }
                    }
                });
            }
        });

        // Quick Add Patient
        $('#quickAddPatientBtn').on('click', function(e) {
            e.preventDefault();
            $('#quickPatientForm')[0].reset();
            $('#quickAddPatientModal').modal('show');
        });

        $('#quickPatientForm').on('submit', function(e) {
            e.preventDefault();
            const name = $('#quick_patient_name').val().trim();
            const phone = $('#quick_patient_phone').val().trim();
            const age = $('#quick_patient_age').val();
            const gender = $('#quick_patient_gender').val();

            if (!name || !phone) {
                alert('Please fill in patient name and phone.');
                return;
            }

            const newId = 'NEW_' + Date.now();
            const newOption = new Option(`${name} (${phone})`, newId, true, true);
            $(newOption).data('phone', phone).data('age', age).data('sex', gender);
            $('#patient_id').append(newOption).trigger('change');

            $('#patient_phone').val(phone);
            $('#patient_age').val(age);
            $('#patient_sex').val(gender);

            $('#quickAddPatientModal').modal('hide');
        });

        // Helper to get current computed grand total
        function getCurrentGrandTotal() {
            let subtotal = 0;
            selectedItems.forEach(i => {
                const qty = parseFloat(i.qty) || 1;
                const price = parseFloat(i.price) || 0;
                const discPercent = parseFloat(i.discount_percent) || 0;
                const lineBase = qty * price * (1 - (discPercent / 100));

                let itemGst = 0;
                if (isGstSelected()) {
                    const taxes = getItemTaxes(i);
                    taxes.forEach(t => {
                        itemGst += (lineBase * (parseFloat(t.tax_rate) || 0)) / 100;
                    });
                }
                subtotal += (lineBase + itemGst);
            });

            const shipping = parseFloat($('#shipping_charges').val()) || 0;
            const misc = parseFloat($('#misc_charges').val()) || 0;
            const discPercent = parseFloat($('#overall_disc_percent').val()) || 0;
            let discRate = parseFloat($('#overall_disc_rate').val()) || 0;

            if (discPercent > 0) {
                discRate = subtotal * (discPercent / 100);
            }

            return Math.max(0, subtotal + shipping + misc - discRate);
        }

        // Update Payment Details fields
        function updatePaymentAmounts(gTotal) {
            let grandTotal;
            if (typeof gTotal !== 'undefined' && gTotal !== null && !isNaN(gTotal)) {
                grandTotal = parseFloat(gTotal);
            } else {
                grandTotal = parseFloat($('#displayGrandTotal').text().replace(/[^\d.]/g, '')) || 0;
                if (grandTotal === 0 && typeof getCurrentGrandTotal === 'function') {
                    grandTotal = getCurrentGrandTotal();
                }
            }
            const mode = $('#payment_method').val() || 'cash';
            const paidType = $('#paid_type').val() || 'full';

            // Highlight active button
            $('.pay-mode-btn').removeClass('active');
            $(`.pay-mode-btn[data-mode="${mode}"]`).addClass('active');

            if (mode === 'pay_later') {
                $('#paymentDetailsSection').addClass('d-none');
                $('#enter_amount').val('0.00');
                $('#pending_amount').val(grandTotal.toFixed(2));
                $('#cash_amount').val('0.00');
                $('#online_amount').val('0.00');
                $('#cash_online_pending').val(grandTotal.toFixed(2));
                return;
            }

            $('#paymentDetailsSection').removeClass('d-none');

            // Bank Row visibility
            if (mode === 'debit' || mode === 'upi' || mode === 'cash_online') {
                $('#bankSelectionRow').removeClass('d-none');
            } else {
                $('#bankSelectionRow').addClass('d-none');
            }

            // Amount Inputs configuration
            if (mode === 'cash_online') {
                $('#singleAmountRow').addClass('d-none');
                $('#cashOnlineAmountRow').removeClass('d-none');

                if (paidType === 'full') {
                    const half = (grandTotal / 2);
                    $('#cash_amount').val(half.toFixed(2));
                    $('#online_amount').val((grandTotal - half).toFixed(2));
                    $('#cash_online_pending').val('0.00');
                } else {
                    const c = parseFloat($('#cash_amount').val()) || 0;
                    const o = parseFloat($('#online_amount').val()) || 0;
                    const rem = Math.max(0, grandTotal - (c + o));
                    $('#cash_online_pending').val(rem.toFixed(2));
                }
            } else {
                $('#cashOnlineAmountRow').addClass('d-none');
                $('#singleAmountRow').removeClass('d-none');

                if (paidType === 'full') {
                    $('#enter_amount').val(grandTotal.toFixed(2)).prop('readonly', true);
                    $('#pending_amount').val('0.00');
                } else {
                    $('#enter_amount').prop('readonly', false);
                    const enterVal = parseFloat($('#enter_amount').val()) || 0;
                    const rem = Math.max(0, grandTotal - enterVal);
                    $('#pending_amount').val(rem.toFixed(2));
                }
            }
        }

        window.updatePaymentAmounts = updatePaymentAmounts;
        window.getCurrentGrandTotal = getCurrentGrandTotal;

        // Init on page load
        updatePaymentAmounts();

        // Payment Mode buttons click
        $('.pay-mode-btn').on('click', function() {
            $('#payment_method').val($(this).data('mode'));
            updatePaymentAmounts();
        });

        // Paid type change
        $('#paid_type').on('change', function() {
            const pt = $(this).val();
            const grandTotal = getCurrentGrandTotal();
            if (pt === 'full') {
                $('#enter_amount').val(grandTotal.toFixed(2)).prop('readonly', true);
                $('#pending_amount').val('0.00');
                const half = (grandTotal / 2);
                $('#cash_amount').val(half.toFixed(2));
                $('#online_amount').val((grandTotal - half).toFixed(2));
                $('#cash_online_pending').val('0.00');
            } else {
                $('#enter_amount').prop('readonly', false);
            }
            updatePaymentAmounts();
        });

        // Enter amount input
        $('#enter_amount').on('input', function() {
            const grandTotal = getCurrentGrandTotal();
            const enterVal = parseFloat($(this).val()) || 0;
            const rem = Math.max(0, grandTotal - enterVal);
            $('#pending_amount').val(rem.toFixed(2));
        });

        // Cash amount / Online amount input
        $('#cash_amount, #online_amount').on('input', function() {
            const grandTotal = getCurrentGrandTotal();
            const c = parseFloat($('#cash_amount').val()) || 0;
            const o = parseFloat($('#online_amount').val()) || 0;
            const rem = Math.max(0, grandTotal - (c + o));
            $('#cash_online_pending').val(rem.toFixed(2));
        });

        // Cancel button
        $('#btnCancelPayment').on('click', function(e) {
            e.preventDefault();
            $('#payment_method').val('cash');
            $('#paid_type').val('full');
            $('#bank_id').val('').trigger('change');
            $('#cash_amount').val('0.00');
            $('#online_amount').val('0.00');
            updatePaymentAmounts();
        });

        // Open Add Bank modal
        $('#btnOpenAddBankModal').on('click', function(e) {
            e.preventDefault();
            $('#addBankModal').modal('show');
        });

        // Save Bank via AJAX
        $('#saveBankBtn').on('click', function() {
            const name = $('#ajax_bank_name').val().trim();
            const acct = $('#ajax_account_number').val().trim();
            if (!name || !acct) {
                alert('Please fill in Bank Name and Account Number.');
                return;
            }
            const btn = $(this);
            btn.prop('disabled', true).text('Saving...');
            $.ajax({
                url: '{{ route("banks.ajax_store") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                data: {
                    bank_name: name,
                    account_number: acct,
                    ifsc_code: $('#ajax_ifsc_code').val(),
                    branch_name: $('#ajax_branch_name').val(),
                    opening_balance: $('#ajax_opening_balance').val() || 0
                },
                success: function(res) {
                    btn.prop('disabled', false).text('Save Bank');
                    if (res.status && res.bank) {
                        const b = res.bank;
                        const newOpt = new Option(`${b.bank_name || b.name} (${b.account_number})`, b.id, true, true);
                        $('#bank_id').append(newOpt).trigger('change');
                        $('#addBankModal').modal('hide');
                        document.getElementById('addBankForm').reset();
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Bank added successfully!');
                        }
                    } else {
                        alert(res.message || 'Error saving bank');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Save Bank');
                    alert(xhr.responseJSON ? xhr.responseJSON.message : 'Error adding bank');
                }
            });
        });

        // Product Search Autocomplete
        $('#productSearchInput').on('keyup focus', function() {
            const query = $(this).val().toLowerCase().trim();
            const resultsBox = $('#searchDropdownResults');
            if (query.length < 1) {
                resultsBox.hide().empty();
                return;
            }

            const matches = allMedicines.filter(m => 
                (m.name && m.name.toLowerCase().includes(query)) ||
                (m.barcode && m.barcode.toLowerCase().includes(query)) ||
                (m.batch_no && m.batch_no.toLowerCase().includes(query))
            );

            if (matches.length === 0) {
                resultsBox.html('<div class="p-3 text-center text-muted">No medicines found. Click "+ Add Product" to add new.</div>').show();
                return;
            }

            let html = '';
            matches.slice(0, 15).forEach(m => {
                const imgU = resolveMedicineImageUrl(m.image);
                html += `
                    <div class="search-result-item d-flex align-items-center" data-id="${m.id}" style="padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #f1f5f9;">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #f8fafc; border: 1.5px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-right: 12px; overflow: hidden;">
                            ${imgU ? `<img src="${imgU}" style="width:100%; height:100%; object-fit:cover;" onerror="this.style.display='none';">` : `<i class="fas fa-pills text-muted" style="font-size: 14px;"></i>`}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong style="color: #1e293b; font-size: 13.5px;">${m.name}</strong>
                                <span class="badge" style="background-color: #cfece0; color: #1f513f; font-weight: 600; border-radius: 50px; font-size: 11px;">Stock: ${m.quantity || 0}</span>
                            </div>
                            <div class="text-muted" style="font-size: 11.5px;">
                                Price: ₹${parseFloat(m.mrp || m.purchase_price || 0).toFixed(2)} | Batch: ${m.batch_no || 'N/A'} | Exp: ${m.expiry_date || 'N/A'}
                            </div>
                        </div>
                    </div>
                `;
            });
            resultsBox.html(html).show();
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#productSearchInput, #searchDropdownResults').length) {
                $('#searchDropdownResults').hide();
            }
        });

        $(document).on('click', '.search-result-item', function() {
            const id = $(this).data('id');
            const med = allMedicines.find(m => m.id == id);
            if (med) {
                addItemToSale(med);
            }
            $('#productSearchInput').val('');
            $('#searchDropdownResults').hide();
        });
    });

    function resolveMedicineImageUrl(img) {
        if (!img || typeof img !== 'string') return '';
        const trimmed = img.trim();
        if (trimmed === '') return '';
        if (trimmed.startsWith('http://') || trimmed.startsWith('https://') || trimmed.startsWith('data:')) {
            return trimmed;
        }
        return '{{ url("/") }}/' + trimmed.replace(/^\/+/, '');
    }

        function isGstSelected() {
        const val = $('input[name="gst_option"]:checked').val();
        return val === 'with_gst' || val === 'gst' || val === 'With GST' || $('#radioGst').is(':checked');
    }

    function getItemTaxes(item) {
        if (!isGstSelected()) {
            return [];
        }

        let gstList = null;
        if (item.product_gst) {
            if (typeof item.product_gst === 'string') {
                try {
                    gstList = JSON.parse(item.product_gst);
                } catch (e) {}
            } else if (Array.isArray(item.product_gst)) {
                gstList = item.product_gst;
            }
        }
        if (Array.isArray(gstList) && gstList.length > 0) {
            return gstList.map(t => ({
                tax_name: t.tax_name || 'CGST',
                tax_rate: parseFloat(t.tax_rate) || 0
            }));
        }

        if (item.gst_percent && parseFloat(item.gst_percent) > 0) {
            const found = (allTaxRates || []).find(t => parseFloat(t.tax_rate) === parseFloat(item.gst_percent));
            const taxName = found ? found.tax_name : ((allTaxRates && allTaxRates[0]) ? allTaxRates[0].tax_name : 'CGST');
            return [{
                tax_name: taxName,
                tax_rate: parseFloat(item.gst_percent)
            }];
        }

        if (allTaxRates && allTaxRates.length > 0) {
            return allTaxRates.map(t => ({
                tax_name: t.tax_name || 'CGST',
                tax_rate: parseFloat(t.tax_rate) || 0
            }));
        }

        return [{
            tax_name: 'CGST',
            tax_rate: 12
        }];
    }

    function addItemToSale(med) {
        const existing = selectedItems.find(i => i.id == med.id);
        if (existing) {
            existing.qty += 1;
        } else {
            selectedItems.push({
                id: med.id,
                name: med.name,
                image: resolveMedicineImageUrl(med.image),
                batch_no: med.batch_no || '',
                expiry_date: med.expiry_date || '',
                stock: med.quantity || 0,
                qty: 1,
                price: parseFloat(med.mrp || med.unit || med.purchase_price || 0),
                discount_percent: 0,
                discount_rate: 0,
                gst_percent: (med.gst_option === 'With GST' ? 12 : (med.gst_option ? 12 : 0)),
                product_gst: med.product_gst || null,
                is_custom: 0,
            });
        }
        renderItemsCards();
    }

    // RENDER ITEMS IN A SINGLE BEAUTIFULLY ALIGNED ROW
    function renderItemsCards() {
        const container = $('#saleItemsContainer');

        if (selectedItems.length === 0) {
            container.html(`
                <div id="noItemBox" class="text-center py-5 rounded-4" style="background: #ffffff; border: 1.5px dashed #cbd5e1;">
                    <i class="fas fa-pills fa-3x mb-2 d-block" style="color: #f89884;"></i>
                    <div class="fw-bold" style="color: #64748b; font-size: 14.5px;">Search medicines above or scan barcode to add to bill</div>
                </div>
            `);
            $('#itemsCountBadge').text('Total items : 0');
            recalcTotals();
            return;
        }

        let cardsHtml = '';
        selectedItems.forEach((item, idx) => {
            const qty = parseFloat(item.qty) || 1;
            const price = parseFloat(item.price) || 0;
            const discPercent = parseFloat(item.discount_percent) || 0;
            const lineBase = qty * price * (1 - (discPercent / 100));
            const discRate = (qty * price) * (discPercent / 100);

            let lineGstAmount = 0;
            let badgesHtml = '';
            const itemTaxes = getItemTaxes(item);

            if (itemTaxes.length > 0) {
                badgesHtml = itemTaxes.map(t => {
                    const tRate = parseFloat(t.tax_rate) || 0;
                    const tAmt = (lineBase * tRate) / 100;
                    lineGstAmount += tAmt;
                    return `<span class="badge item-gst-badge" style="background-color: #f8fafc; color: #1e293b; font-weight: 600; border-left: 3px solid #10b981; border-radius: 4px; padding: 4px 8px; font-size: 11px; border: 1px solid #e2e8f0; border-left: 3px solid #10b981; text-transform: none;">${t.tax_name}: ${tRate.toFixed(2)}% (₹${tAmt.toFixed(2)})</span>`;
                }).join(' ');
            }

            const lineTotal = lineBase + lineGstAmount;

            let thumbContent = '';
            if (item.image && typeof item.image === 'string' && item.image.trim() !== '') {
                thumbContent = `
                    <img src="${item.image}" alt="${item.name}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" style="width:100%; height:100%; object-fit:cover; border-radius:10px;">
                    <div style="display:none; width:100%; height:100%; align-items:center; justify-content:center;">
                        <i class="fas fa-pills text-muted" style="font-size: 22px;"></i>
                    </div>
                `;
            } else {
                thumbContent = `<i class="fas fa-pills text-muted" style="font-size: 22px;"></i>`;
            }

            cardsHtml += `
                <div class="sale-item-card" data-index="${idx}">
                    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                        <!-- Thumbnail Box (with space to name) -->
                        <div class="item-card-thumb">
                            ${thumbContent}
                        </div>

                        <!-- Product Title and Stock badge + GST badge (with space to inputs) -->
                        <div class="item-card-info-col">
                            <strong style="font-size: 14.5px; color: #1e293b; display: block; line-height: 1.3;" class="mb-1">${item.name}</strong>
                            <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                                <span class="badge" style="background-color: #cfece0; color: #1f513f; font-weight: 600; border-radius: 50px; padding: 3px 8px; font-size: 11px;">Stock: ${item.stock || 0}</span>
                                ${badgesHtml}
                            </div>
                        </div>

                        <!-- BATCH NO -->
                        <div class="item-card-field" style="flex: 1; min-width: 120px;">
                            <label class="card-input-label">BATCH NO</label>
                            <input type="text" class="form-control form-control-sm item-batch pill-input" value="${item.batch_no || ''}">
                        </div>

                        <!-- EXP -->
                        <div class="item-card-field" style="flex: 1; min-width: 125px;">
                            <label class="card-input-label">EXP</label>
                            <input type="text" class="form-control form-control-sm item-expiry pill-input" value="${item.expiry_date || ''}" placeholder="YYYY-MM-DD">
                        </div>

                        <!-- QTY Stepper -->
                        <div class="item-card-field" style="width: 105px; flex-shrink: 0;">
                            <label class="card-input-label">QTY</label>
                            <div class="qty-stepper-pill">
                                <button type="button" class="stepper-btn qty-minus">-</button>
                                <input type="number" min="1" class="item-qty stepper-input" value="${item.qty}">
                                <button type="button" class="stepper-btn qty-plus">+</button>
                            </div>
                        </div>

                        <!-- MRP -->
                        <div class="item-card-field" style="flex: 1; min-width: 95px;">
                            <label class="card-input-label">MRP</label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm item-price pill-input text-center" value="${item.price.toFixed(2)}">
                        </div>

                        <!-- DISC % -->
                        <div class="item-card-field" style="width: 80px; flex-shrink: 0;">
                            <label class="card-input-label">DISC %</label>
                            <input type="number" min="0" max="100" step="0.01" class="form-control form-control-sm item-disc pill-input text-center" value="${item.discount_percent}">
                        </div>

                        <!-- DISC RATE -->
                        <div class="item-card-field" style="flex: 1; min-width: 95px;">
                            <label class="card-input-label">DISC RATE</label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm item-disc-rate pill-input text-center" value="${discRate.toFixed(2)}">
                        </div>

                        <!-- Large Line Total with INCL. GST -->
                        <div class="item-card-field text-end" style="min-width: 95px; flex-shrink: 0; display: flex; flex-direction: column; justify-content: center;">
                            <label class="card-input-label">&nbsp;</label>
                            <div class="item-card-total-val" style="font-size: 19px; line-height: 1.2;">₹${lineTotal.toFixed(2)}</div>
                            ${isGstSelected() && lineGstAmount > 0 ? `<div style="font-size: 9.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 3px;">INCL. GST</div>` : ''}
                        </div>

                        <!-- Delete Action -->
                        <div style="flex-shrink: 0;">
                            <label class="card-input-label">&nbsp;</label>
                            <button type="button" class="btn-card-delete btn-remove-item" title="Remove Item">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        container.html(cardsHtml);
        $('#itemsCountBadge').text(`Total items : ${selectedItems.length}`);
        recalcTotals();
    }

    // Stepper Plus / Minus Click Handlers
    $(document).on('click', '.qty-minus', function() {
        const card = $(this).closest('.sale-item-card');
        const idx = card.data('index');
        if (selectedItems[idx] && selectedItems[idx].qty > 1) {
            selectedItems[idx].qty -= 1;
            renderItemsCards();
        }
    });

    $(document).on('click', '.qty-plus', function() {
        const card = $(this).closest('.sale-item-card');
        const idx = card.data('index');
        if (selectedItems[idx]) {
            selectedItems[idx].qty += 1;
            renderItemsCards();
        }
    });

    // Inputs change on cards
    $(document).on('input', '.item-qty', function() {
        const card = $(this).closest('.sale-item-card');
        const idx = card.data('index');
        const val = parseInt($(this).val()) || 1;
        if (selectedItems[idx]) {
            selectedItems[idx].qty = val;
            renderItemsCards();
        }
    });

    $(document).on('input', '.item-price', function() {
        const card = $(this).closest('.sale-item-card');
        const idx = card.data('index');
        const val = parseFloat($(this).val()) || 0;
        if (selectedItems[idx]) {
            selectedItems[idx].price = val;
            renderItemsCards();
        }
    });

    $(document).on('input', '.item-disc', function() {
        const card = $(this).closest('.sale-item-card');
        const idx = card.data('index');
        const val = parseFloat($(this).val()) || 0;
        if (selectedItems[idx]) {
            selectedItems[idx].discount_percent = Math.min(100, Math.max(0, val));
            renderItemsCards();
        }
    });

    $(document).on('input', '.item-disc-rate', function() {
        const card = $(this).closest('.sale-item-card');
        const idx = card.data('index');
        const rate = parseFloat($(this).val()) || 0;
        if (selectedItems[idx]) {
            const base = selectedItems[idx].qty * selectedItems[idx].price;
            selectedItems[idx].discount_percent = base > 0 ? (rate / base) * 100 : 0;
            renderItemsCards();
        }
    });

    $(document).on('input', '.item-batch', function() {
        const idx = $(this).closest('.sale-item-card').data('index');
        if (selectedItems[idx]) selectedItems[idx].batch_no = $(this).val();
    });

    $(document).on('input', '.item-expiry', function() {
        const idx = $(this).closest('.sale-item-card').data('index');
        if (selectedItems[idx]) selectedItems[idx].expiry_date = $(this).val();
    });

    $(document).on('click', '.btn-remove-item', function() {
        const idx = $(this).closest('.sale-item-card').data('index');
        selectedItems.splice(idx, 1);
        renderItemsCards();
    });

    function recalcTotals() {
        let subtotal = 0;
        selectedItems.forEach(i => {
            const qty = parseFloat(i.qty) || 1;
            const price = parseFloat(i.price) || 0;
            const discPercent = parseFloat(i.discount_percent) || 0;
            const lineBase = qty * price * (1 - (discPercent / 100));

            let itemGst = 0;
            if (isGstSelected()) {
                const taxes = getItemTaxes(i);
                taxes.forEach(t => {
                    itemGst += (lineBase * (parseFloat(t.tax_rate) || 0)) / 100;
                });
            }
            subtotal += (lineBase + itemGst);
        });

        const shipping = parseFloat($('#shipping_charges').val()) || 0;
        const misc = parseFloat($('#misc_charges').val()) || 0;
        const discPercent = parseFloat($('#overall_disc_percent').val()) || 0;
        let discRate = parseFloat($('#overall_disc_rate').val()) || 0;

        if (discPercent > 0) {
            discRate = subtotal * (discPercent / 100);
            $('#overall_disc_rate').val(discRate.toFixed(2));
        }

        const grandTotal = Math.max(0, subtotal + shipping + misc - discRate);

        $('#displaySubtotal').text('₹' + subtotal.toFixed(2));
        $('#displayGrandTotal').text('₹' + grandTotal.toFixed(2));
        $('#footerTotalAmount').text('₹' + grandTotal.toFixed(2));
        if (typeof window.updatePaymentAmounts === 'function') {
            window.updatePaymentAmounts(grandTotal);
        } else if (typeof updatePaymentAmounts === 'function') {
            updatePaymentAmounts(grandTotal);
        }
    }

    $('#shipping_charges, #misc_charges, #overall_disc_percent, #overall_disc_rate').on('input', recalcTotals);
    $('input[name="gst_option"]').on('change', function() {
        renderItemsCards();
        recalcTotals();
    });

    // Form Submit
    function submitSale(isNewAfter = false) {
        if (selectedItems.length === 0) {
            alert('Please add at least one medicine/product to generate bill.');
            return;
        }

        let subtotal = 0;
        selectedItems.forEach(i => {
            const qty = parseFloat(i.qty) || 1;
            const price = parseFloat(i.price) || 0;
            const discPercent = parseFloat(i.discount_percent) || 0;
            const lineBase = qty * price * (1 - (discPercent / 100));

            let itemGst = 0;
            if (isGstSelected()) {
                const taxes = getItemTaxes(i);
                taxes.forEach(t => {
                    itemGst += (lineBase * (parseFloat(t.tax_rate) || 0)) / 100;
                });
            }
            subtotal += (lineBase + itemGst);
        });

        const shipping = parseFloat($('#shipping_charges').val()) || 0;
        const misc = parseFloat($('#misc_charges').val()) || 0;
        const discRate = parseFloat($('#overall_disc_rate').val()) || 0;
        const grandTotal = Math.max(0, subtotal + shipping + misc - discRate);

        const payload = {
            patient_id: $('#patient_id').val() && !$('#patient_id').val().startsWith('NEW_') ? $('#patient_id').val() : null,
            patient_name: $('#patient_id').val() && $('#patient_id').val().startsWith('NEW_') ? $('#patient_id option:selected').text().split('(')[0].trim() : ($('#patient_id option:selected').text().split('(')[0].trim() || 'Walk-in Patient'),
            patient_phone: $('#patient_phone').val(),
            patient_age: $('#patient_age').val(),
            patient_sex: $('#patient_sex').val(),
            patient_bp: $('#patient_bp').val(),
            patient_temperature: $('#patient_temperature').val(),
            patient_pulse: $('#patient_pulse').val(),
            patient_history: $('#patient_history').val(),
            patient_complaint: $('#patient_complaint').val(),
            advice_for_lab_tests: $('#advice_for_lab_tests').val(),
            order_date: $('#order_date').val(),
            gst_option: $('input[name="gst_option"]:checked').val(),
            payment_method: $('#payment_method').val(),
            paid_type: $('#paid_type').val() || 'full',
            enter_amount: parseFloat($('#enter_amount').val()) || 0,
            cash_amount: parseFloat($('#cash_amount').val()) || 0,
            online_amount: parseFloat($('#online_amount').val()) || 0,
            bank_id: $('#bank_id').val() || null,
            shipping: shipping,
            misc_charges: misc,
            discount: discRate,
            subtotal: subtotal,
            total: grandTotal,
            remarks: $('#remarks').val(),
            branch_id: saleBranchId,
            items: selectedItems.map(i => {
                const qty = parseFloat(i.qty) || 1;
                const price = parseFloat(i.price) || 0;
                const discPercent = parseFloat(i.discount_percent) || 0;
                const lineBase = qty * price * (1 - (discPercent / 100));

                let lineGst = 0;
                let taxesDetail = [];
                if (isGstSelected()) {
                    const taxes = getItemTaxes(i);
                    taxes.forEach(t => {
                        const r = parseFloat(t.tax_rate) || 0;
                        const a = (lineBase * r) / 100;
                        lineGst += a;
                        taxesDetail.push({
                            tax_name: t.tax_name,
                            tax_rate: r,
                            tax_amount: a
                        });
                    });
                }
                return {
                    product_id: i.id,
                    name: i.name,
                    batch_no: i.batch_no,
                    expiry_date: i.expiry_date,
                    quantity: qty,
                    price: price,
                    mrp: price,
                    discount_percent: discPercent,
                    discount_price: (qty * price) * (discPercent / 100),
                    product_gst_details: taxesDetail,
                    product_gst_total: lineGst,
                    gst_percent: taxesDetail.length > 0 ? taxesDetail[0].tax_rate : 0,
                    total: lineBase + lineGst
                };
            }),
        };

        $('#btnGenerateBill, #btnGenerateBillNew').prop('disabled', true);

        $.ajax({
            url: '{{ url("api/order_sale") }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Authorization': 'Bearer ' + saleAuthToken,
                'Accept': 'application/json'
            },
            data: payload,
            success: function(res) {
                if (res.status) {
                    Swal.fire({
                        title: 'Success!',
                        text: res.message || ('Sale Bill created successfully! Invoice: ' + (res.order_number || ('#' + res.order_id))),
                        icon: 'success',
                        confirmButtonColor: '#f89884',
                        confirmButtonText: 'View Invoice',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        const targetInvoice = res.invoice_url || ('/sales-invoice/' + res.order_id);
                        if (isNewAfter) {
                            window.open(targetInvoice, '_blank');
                            window.location.reload();
                        } else {
                            window.location.href = targetInvoice;
                        }
                    });
                } else {
                    Swal.fire('Error', res.message || 'Error creating sale bill', 'error');
                    $('#btnGenerateBill, #btnGenerateBillNew').prop('disabled', false);
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error submitting sale';
                Swal.fire('Error', msg, 'error');
                $('#btnGenerateBill, #btnGenerateBillNew').prop('disabled', false);
            }
        });
    }
</script>
@endsection