@extends('layout.app')

@section('title', 'Edit Sale Bill')

@section('content')
<style>
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
        padding: 28px 32px !important;
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

    /* All form controls enabled, editable, with 50% radius */
    .form-control, .form-select {
        height: 40px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 50px !important;
        padding: 6px 18px !important;
        font-size: 13.5px !important;
        color: #000000 !important;
        background-color: #ffffff !important;
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
        background-color: #ffffff !important;
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

    /* Payment Mode Grid */
    .pay-mode-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px !important;
        margin-bottom: 14px;
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
        height: 42px !important;
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

    /* BEAUTIFUL UNIFIED ITEM CARD */
    .sale-item-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 20px !important;
        margin-bottom: 14px;
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
    }
    .item-card-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
    }
    .item-card-info-col {
        min-width: 180px;
        flex: 2;
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
        background: #ffffff !important;
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
        font-size: 20px;
        font-weight: 800;
        color: #f89884;
        letter-spacing: -0.5px;
        white-space: nowrap;
    }

    #paymentDetailsSection {
        padding: 20px 24px !important;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px !important;
        margin-bottom: 20px;
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <!-- Top Toolbar Header -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <h4 class="page-title mb-0" style="font-weight: 700; font-size: 21px; color: #1e293b;">
                    Edit Sale Bill <span style="color: #f89884;">#{{ $order->order_number ?? $order->id }}</span>
                </h4>

                <div class="d-inline-flex align-items-center gap-2 border-start ps-3" style="margin-left:15px">
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="gst_option" id="radioNoGst" value="without_gst" {{ in_array(strtolower($order->gst_option ?? ''), ['without_gst', 'without gst']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="radioNoGst" style="font-size: 13px; font-weight: 600; cursor: pointer;">No GST</label>
                    </div>
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="gst_option" id="radioGst" value="with_gst" {{ !in_array(strtolower($order->gst_option ?? ''), ['without_gst', 'without gst']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="radioGst" style="font-size: 13px; font-weight: 600; cursor: pointer;">GST</label>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('sales.invoice', $order->id) }}" class="btn btn-submit-main" style="padding: 7px 20px !important; font-size: 13px;margin-right:15px" target="_blank">
                    <i class="fas fa-print me-1"></i> Print Invoice
                </a>
                <a href="{{ route('sales.list') }}" class="btn-back-main">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        @php
            $orderPatient = $order->patients->first();
            $patientFullname = $orderPatient ? ($orderPatient->fullname ?? $orderPatient->name) : ($order->patient_name ?? '');
            $patientPhone = $orderPatient ? ($orderPatient->phone ?? $orderPatient->phone_number) : ($order->patient_phone ?? '');
            $patientAge = $orderPatient ? $orderPatient->age : ($order->patient_age ?? '');
            $patientSex = $orderPatient ? ($orderPatient->gender ?? 'Male') : ($order->patient_sex ?? 'Male');
            $patientBp = $orderPatient ? $orderPatient->bp : ($order->bp ?? '');
            $patientTemp = $orderPatient ? $orderPatient->temperature : ($order->temperature ?? '');
            $patientPulse = $orderPatient ? $orderPatient->pulse : ($order->pulse ?? '');
            $patientHistory = $orderPatient ? $orderPatient->patient_history : ($order->patient_history ?? '');
            $patientComplaint = $orderPatient ? $orderPatient->patient_complaint : ($order->patient_complaint ?? '');
            $patientLab = $orderPatient ? $orderPatient->advice_for_lab_tests : ($order->advice_for_lab_tests ?? '');
            $orderDate = !empty($order->order_date) ? date('Y-m-d', strtotime($order->order_date)) : date('Y-m-d');
            $selectedPatientId = $order->user_id ?? ($orderPatient ? $orderPatient->id : '');
        @endphp

        <form id="editSaleForm">
            @csrf

            <!-- SINGLE UNIFIED CARD FOR ALL SALE FIELDS WITH GENEROUS PADDING -->
            <div class="card single-sale-card">
                <div class="card-body">
                    <!-- Patient Row 1 (Enabled & Editable) -->
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
                                            {{ ($selectedPatientId == $p->id) ? 'selected' : '' }}
                                            data-fullname="{{ $p->fullname }}"
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
                                <input type="text" class="form-control" name="patient_phone" id="patient_phone" placeholder="Phone number" value="{{ $patientPhone }}">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-2 col-6">
                            <div class="form-group-compact">
                                <label><i class="fas fa-calendar-alt text-muted me-1"></i> Age</label>
                                <input type="number" class="form-control" name="patient_age" id="patient_age" placeholder="Age" min="0" max="150" value="{{ $patientAge }}">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-2 col-6">
                            <div class="form-group-compact">
                                <label><i class="fas fa-venus-mars text-muted me-1"></i> Sex</label>
                                <select class="form-control" name="patient_sex" id="patient_sex">
                                    <option value="Male" {{ strtolower($patientSex) === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ strtolower($patientSex) === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ strtolower($patientSex) === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-1 col-12">
                            <div class="form-group-compact">
                                <label><i class="fas fa-heartbeat text-danger me-1"></i> B.P</label>
                                <input type="text" class="form-control" name="patient_bp" id="patient_bp" placeholder="120/80" value="{{ $patientBp }}">
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Vitals & Bill Date (Enabled & Editable) -->
                    <div class="row g-3 mb-2">
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="form-group-compact">
                                <label><i class="fas fa-thermometer-half text-warning me-1"></i> Temperature</label>
                                <input type="text" class="form-control" name="patient_temperature" id="patient_temperature" placeholder="e.g. 98.6" value="{{ $patientTemp }}">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="form-group-compact">
                                <label><i class="fas fa-wave-square text-success me-1"></i> Pulse</label>
                                <input type="text" class="form-control" name="patient_pulse" id="patient_pulse" placeholder="e.g. 72" value="{{ $patientPulse }}">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-12">
                            <div class="form-group-compact">
                                <label><i class="fas fa-file-medical-alt text-muted me-1"></i> Patient History</label>
                                <input type="text" class="form-control" name="patient_history" id="patient_history" placeholder="Patient history" value="{{ $patientHistory }}">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-12">
                            <div class="form-group-compact">
                                <label><i class="fas fa-stethoscope text-muted me-1"></i> Patient Complaint</label>
                                <input type="text" class="form-control" name="patient_complaint" id="patient_complaint" placeholder="Patient complaint" value="{{ $patientComplaint }}">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-12">
                            <div class="form-group-compact">
                                <label><i class="fas fa-vial text-muted me-1"></i> Advice for Lab tests</label>
                                <input type="text" class="form-control" name="advice_for_lab_tests" id="advice_for_lab_tests" placeholder="Advice for lab tests" value="{{ $patientLab }}">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-12">
                            <div class="form-group-compact">
                                <label><i class="fas fa-calendar-day text-muted me-1"></i> Bill Date</label>
                                <input type="date" class="form-control" name="order_date" id="order_date" value="{{ $orderDate }}">
                            </div>
                        </div>
                    </div>

                    <hr style="margin: 18px 0; border-color: #e2e8f0;">

                    <!-- Total items bar & + Add Product button -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge" style="background-color: #f8fafc; border: 1.5px solid #cbd5e1; color: #1e293b; font-size: 13.5px; font-weight: 700; border-radius: 50px; padding: 7px 18px;" id="itemsCountBadge">
                            Total items : {{ count($order->order_items ?? []) }}
                        </span>
                    </div>

                    <!-- Search Input to add more medicines -->
                    <div class="search-input-wrap mb-3">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control" id="productSearchInput" placeholder="Search name, category, batch or barcode to add more medicines..." autocomplete="off">
                        <div id="searchDropdownResults"></div>
                    </div>

                    <!-- Dynamic Item Cards Container (Fully Editable) -->
                    <div id="itemsCardsContainer" class="mb-3">
                        <!-- Populated by JavaScript -->
                    </div>

                    <!-- Charges & Summary Row (Enabled & Editable) -->
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-lg-3 col-6">
                            <div class="form-group-compact">
                                <label>Shipping Charges</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="shipping" id="shipping_charges" value="{{ (float)($order->shipping ?? 0) }}" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="form-group-compact">
                                <label>Misc Charges</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="misc_charges" id="misc_charges" value="{{ (float)($order->misc_charges ?? 0) }}" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="form-group-compact">
                                <label>Discount (&#8377;)</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="discount" id="overall_disc_rate" value="{{ (float)($order->discount ?? 0) }}" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="form-group-compact">
                                <label>Remarks</label>
                                <input type="text" class="form-control" name="remarks" id="remarks" value="{{ $order->remarks ?? '' }}" placeholder="Sale remarks...">
                            </div>
                        </div>
                    </div>

                    <!-- Subtotal & Grand Total Display Bar -->
                    <div class="d-flex justify-content-between align-items-center py-3 px-4 mb-3 rounded-4" style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px;">
                        <div>
                            <span class="text-muted" style="font-size: 14px;">Subtotal:</span>
                            <strong style="color: #1e293b; font-size: 15px;" class="ms-1" id="displaySubtotal">&#8377;{{ number_format($order->total_amount ?? 0, 2) }}</strong>
                        </div>
                        <div>
                            <span style="font-size: 15px; font-weight: 700; color: #1e293b;">Grand Total:</span>
                            <span style="font-size: 20px; font-weight: 800; color: #f89884;" class="ms-1" id="displayGrandTotal">&#8377;{{ number_format($order->total_amount ?? 0, 2) }}</span>
                        </div>
                    </div>

                    <!-- Payment Mode Section -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="far fa-folder-open" style="color: #f89884; font-size: 16px;"></i>
                                <span style="font-size: 13.5px; font-weight: 700; color: #1e293b;">Payment Mode</span>
                            </div>
                            <button type="button" class="btn btn-sm" id="btnCancelPayment" style="border: 1.5px solid #ef4444; color: #ef4444; border-radius: 50px; font-weight: 600; font-size: 12px; padding: 4px 18px; background: transparent;">
                                Reset Payment
                            </button>
                        </div>
                        <div class="pay-mode-container">
                            <div class="pay-mode-btn {{ $order->payment_method === 'pay_later' ? 'active' : '' }}" data-mode="pay_later">
                                <i class="far fa-money-bill-alt"></i>
                                <span>Pay Later</span>
                            </div>
                            <div class="pay-mode-btn {{ in_array($order->payment_method, ['cash', '', null]) ? 'active' : '' }}" data-mode="cash">
                                <i class="far fa-money-bill-alt"></i>
                                <span>Cash</span>
                            </div>
                            <div class="pay-mode-btn {{ in_array($order->payment_method, ['debit', 'debit card']) ? 'active' : '' }}" data-mode="debit">
                                <i class="far fa-credit-card"></i>
                                <span>Debit</span>
                            </div>
                            <div class="pay-mode-btn {{ in_array($order->payment_method, ['cash_online', 'cash+online']) ? 'active' : '' }}" data-mode="cash_online">
                                <i class="far fa-money-bill-alt"></i>
                                <span>Cash+Online</span>
                            </div>
                            <div class="pay-mode-btn {{ in_array($order->payment_method, ['upi', 'online', 'scan']) ? 'active' : '' }}" data-mode="upi">
                                <i class="fas fa-qrcode"></i>
                                <span>UPI Paid</span>
                            </div>
                        </div>
                        <input type="hidden" name="payment_method" id="payment_method" value="{{ $order->payment_method ?? 'cash' }}">

                        <!-- Payment Details Container (50% Radius inputs) -->
                        <div id="paymentDetailsSection">
                            <!-- Paid Type Row -->
                            <div class="row g-3 mb-2" id="paidTypeRow">
                                <div class="col-12">
                                    <div class="form-group-compact">
                                        <label>Paid Type</label>
                                        <select name="paid_type" id="paid_type" class="form-control" style="border-radius: 50px !important;">
                                            <option value="full" {{ ($order->payment_status === 'paid' || (float)$order->remaining_amount <= 0) ? 'selected' : '' }}>Fully</option>
                                            <option value="partial" {{ ($order->payment_status !== 'paid' && (float)$order->remaining_amount > 0) ? 'selected' : '' }}>Partially</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Single Payment Amounts (Cash, Debit, UPI) -->
                            <div class="row g-3 mb-2" id="singleAmountRow">
                                <div class="col-md-6 col-12">
                                    <div class="form-group-compact">
                                        <label>Enter Amount</label>
                                        <input type="number" step="0.01" min="0" class="form-control" name="enter_amount" id="enter_amount" value="{{ number_format(max(0, (float)$order->total_amount - (float)$order->remaining_amount), 2, '.', '') }}" style="border-radius: 50px !important;">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group-compact">
                                        <label>Pending Amount</label>
                                        <input type="text" class="form-control" name="pending_amount" id="pending_amount" value="{{ number_format($order->remaining_amount ?? 0, 2, '.', '') }}" readonly style="border-radius: 50px !important; background-color: #f8fafc;">
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
                                            <input type="text" class="form-control" name="cash_online_pending" id="cash_online_pending" value="{{ number_format($order->remaining_amount ?? 0, 2, '.', '') }}" readonly style="border-radius: 50px !important; background-color: #f8fafc;">
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
                                            <button type="button" class="btn btn-sm" id="btnOpenAddBankModal" style="background-color: #f89884; color: #ffffff; border-radius: 50px; font-size: 11.5px; padding: 3px 14px; font-weight: 600; border: none;">+ Add Bank</button>
                                        </div>
                                        <select name="bank_id" id="bank_id" class="form-control select2" style="border-radius: 50px !important; width: 100%;">
                                            <option value="">Select Bank</option>
                                            @foreach($banks as $b)
                                                <option value="{{ $b->id }}" {{ ($order->bank_id == $b->id) ? 'selected' : '' }}>{{ $b->bank_name ?? $b->name }} ({{ $b->account_number ?? '' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Update Button -->
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-submit-main py-2 px-5 d-flex align-items-center gap-3" id="btnUpdateSale" style="border-radius: 50px !important; font-weight: 700; font-size: 14.5px;">
                                <span>Total Amount : <span id="footerTotalAmount">&#8377;{{ number_format($order->total_amount ?? 0, 2) }}</span></span>
                                <span>Update Sale Bill <i class="fas fa-arrow-right ms-1"></i></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL: ADD BANK ================= -->
<div class="modal fade" id="addBankModal" tabindex="-1" aria-labelledby="addBankModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header d-flex justify-content-between align-items-center" style="background-color: #f89884; color: #ffffff; padding: 16px 24px; border-bottom: none;">
                <h5 class="modal-title font-weight-bold" id="addBankModalLabel" style="color: #ffffff !important; font-size: 17px; margin: 0; font-weight: 700;"><i class="fas fa-university me-2"></i> Add Bank Account</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" style="color: #ffffff !important; opacity: 0.95 !important; font-size: 26px !important; font-weight: 300 !important; text-shadow: none !important; border: none !important; background: transparent !important; padding: 0 !important; cursor: pointer; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form id="ajaxAddBankForm">
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
                            <label class="form-label" style="font-weight: 600; font-size: 12.5px;">Opening Balance (&#8377;)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="ajax_opening_balance" name="opening_balance" value="0.00" style="border-radius: 50px !important;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer p-3 bg-white d-flex justify-content-end gap-2" style="border-top: 1.5px solid #f1f5f9;">
                <button type="button" class="btn text-white fw-bold px-4" id="btnSaveBankAjax" style="background-color: #f89884; border-radius: 50px; font-size: 13.5px; border: none; padding: 8px 24px;">Save Bank</button>
                <button type="button" class="btn text-white px-4 fw-bold" data-bs-dismiss="modal" data-dismiss="modal" style="background-color: #64748b; border-radius: 50px; font-size: 13.5px; border: none; padding: 8px 24px;">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL: QUICK ADD PATIENT ================= -->
<div class="modal fade" id="quickAddPatientModal" tabindex="-1" aria-labelledby="quickAddPatientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: #ffffff; border-bottom: 1.5px solid #f1f5f9; padding: 16px 26px;">
                <h5 class="modal-title font-weight-bold" id="quickAddPatientModalLabel" style="color: #1e293b; font-size: 18px; margin: 0; font-weight: 700;">Add New Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" style="border:none; background:transparent; font-size: 18px; color: #ef4444; padding: 0 4px; cursor: pointer;">
                    <i class="fa fa-times text-danger"></i>
                </button>
            </div>
            <div class="modal-body p-4">
                <form id="quickPatientForm">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" style="font-weight: 600; font-size: 12.5px;">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="new_patient_name" required placeholder="Patient full name" style="border-radius: 50px !important;">
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label" style="font-weight: 600; font-size: 12.5px;">Phone Number</label>
                            <input type="text" class="form-control" id="new_patient_phone" placeholder="Phone number" style="border-radius: 50px !important;">
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label" style="font-weight: 600; font-size: 12.5px;">Age</label>
                            <input type="number" class="form-control" id="new_patient_age" placeholder="Age" min="0" max="150" style="border-radius: 50px !important;">
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label" style="font-weight: 600; font-size: 12.5px;">Gender</label>
                            <select class="form-control" id="new_patient_gender" style="border-radius: 50px !important;">
                                <option value="Male" selected>Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label" style="font-weight: 600; font-size: 12.5px;">B.P</label>
                            <input type="text" class="form-control" id="new_patient_bp" placeholder="120/80" style="border-radius: 50px !important;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer p-3 bg-white d-flex justify-content-end gap-2" style="border-top: 1.5px solid #f1f5f9;">
                <button type="button" class="btn text-white fw-bold px-4" id="btnSaveQuickPatient" style="background-color: #f89884; border-radius: 50px; font-size: 13.5px; border: none; padding: 8px 24px;">Add Patient</button>
                <button type="button" class="btn text-white px-4 fw-bold" data-bs-dismiss="modal" data-dismiss="modal" style="background-color: #64748b; border-radius: 50px; font-size: 13.5px; border: none; padding: 8px 24px;">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    var globalTaxes = @json($taxes ?? []);
    var saleAuthToken = (typeof token !== 'undefined' && token) ? token : (localStorage.getItem('token') || '');
    var saleBranchId = {{ $order->branch_id ?? 1 }};
    var orderId = {{ $order->id }};

    // Pre-populate existing order items into selectedItems array
    var selectedItems = [
        @foreach($order->order_items as $item)
        @php
            $prod = $item->product;
            $unitP = (float)($item->price ?? ($item->mrp ?? 0));
            $discP = (float)($item->product_discount ?? 0);
            $qtyV = (float)($item->quantity ?? 1);
            $gstDet = $item->product_gst_details;
            if (is_string($gstDet)) {
                $gstDet = json_decode($gstDet, true);
            }
        @endphp
        {
            id: {{ $item->product_id ? $item->product_id : 'null' }},
            name: @json($prod ? $prod->name : ($item->custom_product_name ?? 'Medicine')),
            batch_no: @json($item->batch_number ?? ($prod ? $prod->batch_no : '')),
            expiry_date: @json($item->expiry_date ?? ($prod ? $prod->expiry_date : '')),
            qty: {{ $qtyV }},
            price: {{ $unitP }},
            discount_percent: {{ $discP }},
            image: @json($prod ? $prod->image : ''),
            quantity: {{ $prod ? (int)($prod->quantity ?? 0) : 0 }},
            gst_option: @json($prod ? ($prod->gst_option ?? 'Without GST') : 'Without GST'),
            taxes: @json(is_array($gstDet) ? $gstDet : [])
        },
        @endforeach
    ];

    // Helper: is GST active on the form?
    function isGstSelected() {
        return $('input[name="gst_option"]:checked').val() === 'with_gst';
    }

    // Helper: Format Money
    function formatMoney(num) {
        return parseFloat(num || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Helper: Get Applicable Taxes for an Item
    function getItemTaxes(item) {
        if (!isGstSelected()) return [];
        if (item.taxes && Array.isArray(item.taxes) && item.taxes.length > 0) {
            return item.taxes;
        }
        if (globalTaxes && globalTaxes.length > 0) {
            return globalTaxes;
        }
        return [{ tax_name: 'CGST', tax_rate: 12 }];
    }

    // Render Items Cards
    function renderItemsCards() {
        const container = $('#itemsCardsContainer');
        container.empty();
        $('#itemsCountBadge').text('Total items : ' + selectedItems.length);

        if (selectedItems.length === 0) {
            container.html(`
                <div class="text-center py-4 text-muted bg-light rounded-4" style="border: 1.5px dashed #cbd5e1;">
                    <i class="fas fa-pills fa-2x mb-2 text-warning"></i>
                    <div class="fw-bold">No medicines added to this bill. Search above to add medicines.</div>
                </div>
            `);
            recalcTotals();
            return;
        }

        selectedItems.forEach((item, index) => {
            const qty = parseFloat(item.qty) || 1;
            const price = parseFloat(item.price) || 0;
            const discPercent = parseFloat(item.discount_percent) || 0;
            const lineBase = qty * price * (1 - (discPercent / 100));
            const discRate = (qty * price) * (discPercent / 100);

            let itemGst = 0;
            let gstBadgesHtml = '';
            if (isGstSelected()) {
                const taxes = getItemTaxes(item);
                taxes.forEach(t => {
                    const r = parseFloat(t.tax_rate) || 0;
                    const a = (lineBase * r) / 100;
                    itemGst += a;
                    gstBadgesHtml += `
                        <span class="badge" style="background-color: #f8fafc; color: #1e293b; font-weight: 600; border-left: 3px solid #10b981; border-radius: 4px; padding: 4px 8px; font-size: 11px; border: 1px solid #e2e8f0; border-left: 3px solid #10b981;">
                            ${t.tax_name || 'CGST'}: ${r.toFixed(2)}% (&#8377;${a.toFixed(2)})
                        </span>
                    `;
                });
            }

            const lineTotal = lineBase + itemGst;

            let imgSrc = '';
            if (item.image) {
                const raw = String(item.image).trim();
                imgSrc = (raw.startsWith('http://') || raw.startsWith('https://') || raw.startsWith('data:')) ? raw : ('/' + raw.replace(/^\/+/, ''));
            }

            const cardHtml = `
                <div class="sale-item-card" data-index="${index}">
                    <div class="d-flex align-items-center flex-wrap" style="gap: 7px;">
                        <!-- Thumbnail -->
                        <div class="item-card-thumb">
                            ${imgSrc ? `
                                <img src="${imgSrc}" alt="${item.name}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display:none; width:100%; height:100%; align-items:center; justify-content:center;">
                                    <i class="fas fa-pills text-muted" style="font-size: 20px;"></i>
                                </div>
                            ` : `
                                <i class="fas fa-pills text-muted" style="font-size: 20px;"></i>
                            `}
                        </div>

                        <!-- Name & Stock -->
                        <div class="item-card-info-col">
                            <strong style="font-size: 14.5px; color: #1e293b; display: block; line-height: 1.3;" class="mb-1">${item.name}</strong>
                            <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                                <span class="badge" style="background-color: #cfece0; color: #1f513f; font-weight: 600; border-radius: 50px; padding: 3px 8px; font-size: 11px;">
                                    Stock: ${item.quantity !== undefined ? item.quantity : '-'}
                                </span>
                                ${gstBadgesHtml}
                            </div>
                        </div>

                        <!-- BATCH NO (Editable) -->
                        <div style="min-width: 120px; flex: 1;">
                            <label class="card-input-label">BATCH NO</label>
                            <input type="text" class="pill-input item-batch" value="${item.batch_no || ''}" placeholder="Batch No">
                        </div>

                        <!-- EXP (Editable) -->
                        <div style="min-width: 120px; flex: 1;">
                            <label class="card-input-label">EXP</label>
                            <input type="text" class="pill-input item-exp" value="${item.expiry_date || ''}" placeholder="YYYY-MM-DD">
                        </div>

                        <!-- QTY (Editable Stepper) -->
                        <div style="width: 115px; flex-shrink: 0;">
                            <label class="card-input-label text-center">QTY</label>
                            <div class="qty-stepper-pill">
                                <button type="button" class="stepper-btn btn-stepper-minus">-</button>
                                <input type="number" min="1" step="1" class="stepper-input item-qty" value="${qty}">
                                <button type="button" class="stepper-btn btn-stepper-plus">+</button>
                            </div>
                        </div>

                        <!-- MRP (Editable) -->
                        <div style="min-width: 95px; flex: 1;">
                            <label class="card-input-label text-center">MRP</label>
                            <input type="number" step="0.01" min="0" class="pill-input item-price text-center" value="${price.toFixed(2)}">
                        </div>

                        <!-- DISC % (Editable) -->
                        <div style="width: 80px; flex-shrink: 0;">
                            <label class="card-input-label text-center">DISC %</label>
                            <input type="number" step="0.01" min="0" max="100" class="pill-input item-disc-percent text-center" value="${discPercent}">
                        </div>

                        <!-- DISC RATE (Dynamic) -->
                        <div style="min-width: 95px; flex: 1;">
                            <label class="card-input-label text-center">DISC RATE</label>
                            <input type="text" class="pill-input text-center item-disc-rate" value="&#8377;${discRate.toFixed(2)}" readonly style="background-color: #f8fafc !important;">
                        </div>

                        <!-- Line Total -->
                        <div style="text-align: right;">
                            <label class="card-input-label text-end">&nbsp;</label>
                            <div class="item-card-total-val">&#8377;${lineTotal.toFixed(2)}</div>
                            ${isGstSelected() ? '<div style="font-size: 9.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">INCL. GST</div>' : ''}
                        </div>

                        <!-- Delete Button -->
                        <div style="flex-shrink: 0;">
                            <label class="card-input-label">&nbsp;</label>
                            <button type="button" class="btn-card-delete btn-remove-item" title="Remove Item">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.append(cardHtml);
        });

        recalcTotals();
    }

    // Recalculate Totals
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
        const discRate = parseFloat($('#overall_disc_rate').val()) || 0;
        const grandTotal = Math.max(0, subtotal + shipping + misc - discRate);

        $('#displaySubtotal').html('&#8377;' + subtotal.toFixed(2));
        $('#displayGrandTotal').html('&#8377;' + grandTotal.toFixed(2));
        $('#footerTotalAmount').html('&#8377;' + grandTotal.toFixed(2));

        updatePaymentAmounts(grandTotal);
    }

    // Update Payment Inputs based on Grand Total and Mode
    function updatePaymentAmounts(gTotal) {
        window.updatePaymentAmounts = updatePaymentAmounts;
        if (typeof gTotal === 'undefined') {
            const subtotal = parseFloat($('#displaySubtotal').text().replace(/[^\d.]/g, '')) || 0;
            const shipping = parseFloat($('#shipping_charges').val()) || 0;
            const misc = parseFloat($('#misc_charges').val()) || 0;
            const discRate = parseFloat($('#overall_disc_rate').val()) || 0;
            gTotal = Math.max(0, subtotal + shipping + misc - discRate);
        }

        const mode = $('#payment_method').val() || 'cash';
        const paidType = $('#paid_type').val() || 'full';

        if (mode === 'pay_later') {
            $('#paidTypeRow, #singleAmountRow, #cashOnlineAmountRow, #bankSelectionRow').addClass('d-none');
            $('#enter_amount').val('0.00');
            $('#pending_amount').val(gTotal.toFixed(2));
            return;
        }

        $('#paidTypeRow').removeClass('d-none');

        if (mode === 'cash_online') {
            $('#singleAmountRow').addClass('d-none');
            $('#cashOnlineAmountRow').removeClass('d-none');
            $('#bankSelectionRow').removeClass('d-none');

            if (paidType === 'full') {
                const half = (gTotal / 2).toFixed(2);
                $('#cash_amount').val(half);
                $('#online_amount').val((gTotal - parseFloat(half)).toFixed(2));
                $('#cash_online_pending').val('0.00');
            } else {
                const cash = parseFloat($('#cash_amount').val()) || 0;
                const online = parseFloat($('#online_amount').val()) || 0;
                const pend = Math.max(0, gTotal - (cash + online));
                $('#cash_online_pending').val(pend.toFixed(2));
            }
        } else {
            // cash, debit, upi
            $('#cashOnlineAmountRow').addClass('d-none');
            $('#singleAmountRow').removeClass('d-none');

            if (mode === 'debit' || mode === 'upi') {
                $('#bankSelectionRow').removeClass('d-none');
            } else {
                $('#bankSelectionRow').addClass('d-none');
            }

            if (paidType === 'full') {
                $('#enter_amount').val(gTotal.toFixed(2));
                $('#pending_amount').val('0.00');
            } else {
                const entered = parseFloat($('#enter_amount').val()) || 0;
                const pend = Math.max(0, gTotal - entered);
                $('#pending_amount').val(pend.toFixed(2));
            }
        }
    }

    $(document).ready(function() {
        // Init Select2
        if (typeof $.fn.select2 !== 'undefined') {
            $('#patient_id, #bank_id').select2({
                placeholder: 'Select option',
                allowClear: true,
                width: '100%'
            });
        }

        // Render initial items cards
        renderItemsCards();

        // GST Option Change
        $('input[name="gst_option"]').on('change', function() {
            renderItemsCards();
        });

        // Patient selection change: automatically populate all vitals & details
        $('#patient_id').on('change', function() {
            const opt = $(this).find('option:selected');
            if (opt.val()) {
                $('#patient_phone').val(opt.data('phone') || '');
                $('#patient_age').val(opt.data('age') || '');
                if (opt.data('sex')) {
                    $('#patient_sex').val(opt.data('sex'));
                }
                $('#patient_bp').val(opt.data('bp') || '');
                $('#patient_temperature').val(opt.data('temp') || '');
                $('#patient_pulse').val(opt.data('pulse') || '');
                $('#patient_history').val(opt.data('history') || '');
                $('#patient_complaint').val(opt.data('complaint') || '');
                $('#advice_for_lab_tests').val(opt.data('lab') || '');
            }
        });

        // Item Card Input Changes (Real-time updates)
        $(document).on('input change', '.item-qty', function() {
            const idx = $(this).closest('.sale-item-card').data('index');
            const v = Math.max(1, parseFloat($(this).val()) || 1);
            selectedItems[idx].qty = v;
            renderItemsCards();
        });

        $(document).on('input change', '.item-price', function() {
            const idx = $(this).closest('.sale-item-card').data('index');
            const v = Math.max(0, parseFloat($(this).val()) || 0);
            selectedItems[idx].price = v;
            renderItemsCards();
        });

        $(document).on('input change', '.item-disc-percent', function() {
            const idx = $(this).closest('.sale-item-card').data('index');
            const v = Math.min(100, Math.max(0, parseFloat($(this).val()) || 0));
            selectedItems[idx].discount_percent = v;
            renderItemsCards();
        });

        $(document).on('input change', '.item-batch', function() {
            const idx = $(this).closest('.sale-item-card').data('index');
            selectedItems[idx].batch_no = $(this).val();
        });

        $(document).on('input change', '.item-exp', function() {
            const idx = $(this).closest('.sale-item-card').data('index');
            selectedItems[idx].expiry_date = $(this).val();
        });

        // Stepper Buttons (+ / -)
        $(document).on('click', '.btn-stepper-plus', function() {
            const idx = $(this).closest('.sale-item-card').data('index');
            selectedItems[idx].qty = (parseFloat(selectedItems[idx].qty) || 1) + 1;
            renderItemsCards();
        });

        $(document).on('click', '.btn-stepper-minus', function() {
            const idx = $(this).closest('.sale-item-card').data('index');
            const curr = parseFloat(selectedItems[idx].qty) || 1;
            if (curr > 1) {
                selectedItems[idx].qty = curr - 1;
                renderItemsCards();
            }
        });

        // Remove Item
        $(document).on('click', '.btn-remove-item', function() {
            const idx = $(this).closest('.sale-item-card').data('index');
            selectedItems.splice(idx, 1);
            renderItemsCards();
        });

        // Recalculate when Charges change
        $('#shipping_charges, #misc_charges, #overall_disc_rate').on('input', function() {
            recalcTotals();
        });

        // Payment Mode Buttons
        $('.pay-mode-btn').on('click', function() {
            $('.pay-mode-btn').removeClass('active');
            $(this).addClass('active');
            const mode = $(this).data('mode');
            $('#payment_method').val(mode);
            recalcTotals();
        });

        // Paid Type dropdown
        $('#paid_type').on('change', function() {
            recalcTotals();
        });

        // Enter amount input
        $('#enter_amount').on('input', function() {
            const grandTotal = parseFloat($('#displayGrandTotal').text().replace(/[^\d.]/g, '')) || 0;
            const entered = parseFloat($(this).val()) || 0;
            const pend = Math.max(0, grandTotal - entered);
            $('#pending_amount').val(pend.toFixed(2));
        });

        // Cash + Online split inputs
        $('#cash_amount, #online_amount').on('input', function() {
            const grandTotal = parseFloat($('#displayGrandTotal').text().replace(/[^\d.]/g, '')) || 0;
            const cash = parseFloat($('#cash_amount').val()) || 0;
            const online = parseFloat($('#online_amount').val()) || 0;
            const pend = Math.max(0, grandTotal - (cash + online));
            $('#cash_online_pending').val(pend.toFixed(2));
        });

        // Reset Payment button
        $('#btnCancelPayment').on('click', function() {
            $('.pay-mode-btn').removeClass('active');
            $('.pay-mode-btn[data-mode="cash"]').addClass('active');
            $('#payment_method').val('cash');
            $('#paid_type').val('full');
            recalcTotals();
        });

        // Live Search for adding more medicines
        let searchTimeout = null;
        $('#productSearchInput').on('keyup input', function() {
            clearTimeout(searchTimeout);
            const query = $(this).val().trim();
            const resultsBox = $('#searchDropdownResults');

            if (query.length < 2) {
                resultsBox.hide().empty();
                return;
            }

            searchTimeout = setTimeout(function() {
                $.ajax({
                    url: '/api/get_medicines',
                    type: 'GET',
                    data: { search: query, branch_id: saleBranchId },
                    headers: { 'Authorization': 'Bearer ' + saleAuthToken },
                    success: function(res) {
                        let list = [];
                        if (Array.isArray(res)) list = res;
                        else if (res && res.data && Array.isArray(res.data)) list = res.data;

                        if (list.length === 0) {
                            resultsBox.html('<div class="p-3 text-muted text-center">No medicines found.</div>').show();
                            return;
                        }

                        let html = '';
                        list.forEach(m => {
                            const price = parseFloat(m.mrp || m.price || 0).toFixed(2);
                            const stock = m.quantity !== undefined ? m.quantity : 0;
                            html += `
                                <div class="search-result-item d-flex justify-content-between align-items-center" data-id="${m.id}" data-json='${JSON.stringify(m).replace(/'/g, "&#39;")}'>
                                    <div>
                                        <strong style="color: #1e293b; font-size: 13.5px;">${m.name}</strong>
                                        <div class="text-muted" style="font-size: 11.5px;">Batch: ${m.batch_no || 'N/A'} | Exp: ${m.expiry_date || 'N/A'}</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge" style="background-color: #cfece0; color: #1f513f;">Stock: ${stock}</span>
                                        <div class="fw-bold" style="color: #f89884; font-size: 14px;">&#8377;${price}</div>
                                    </div>
                                </div>
                            `;
                        });
                        resultsBox.html(html).show();
                    },
                    error: function() {
                        resultsBox.hide().empty();
                    }
                });
            }, 250);
        });

        // Click search result to add medicine
        $(document).on('click', '.search-result-item', function() {
            const dataStr = $(this).attr('data-json');
            if (dataStr) {
                const m = JSON.parse(dataStr);
                const existing = selectedItems.find(i => i.id == m.id);
                if (existing) {
                    existing.qty = (parseFloat(existing.qty) || 1) + 1;
                } else {
                    selectedItems.push({
                        id: m.id,
                        name: m.name,
                        batch_no: m.batch_no || '',
                        expiry_date: m.expiry_date || '',
                        qty: 1,
                        price: parseFloat(m.mrp || m.price || 0),
                        discount_percent: 0,
                        image: m.image || '',
                        quantity: m.quantity !== undefined ? m.quantity : 0,
                        gst_option: m.gst_option || 'Without GST',
                        taxes: m.taxes || []
                    });
                }
                $('#productSearchInput').val('');
                $('#searchDropdownResults').hide().empty();
                renderItemsCards();
            }
        });

        // Hide search dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-input-wrap').length) {
                $('#searchDropdownResults').hide();
            }
        });

        // Quick Add Patient Modal
        $('#quickAddPatientBtn').on('click', function() {
            $('#quickPatientForm')[0].reset();
            $('#quickAddPatientModal').modal('show');
        });

        $('#btnSaveQuickPatient').on('click', function() {
            const name = $('#new_patient_name').val().trim();
            if (!name) {
                alert('Please enter patient name.');
                return;
            }
            const phone = $('#new_patient_phone').val().trim();
            const age = $('#new_patient_age').val().trim();
            const sex = $('#new_patient_gender').val();
            const bp = $('#new_patient_bp').val().trim();

            const tempId = 'NEW_' + Date.now();
            const newOpt = new Option(`${name} (${phone || 'No phone'})`, tempId, true, true);
            $(newOpt).data('fullname', name)
                    .data('phone', phone)
                    .data('age', age)
                    .data('sex', sex)
                    .data('bp', bp);
            $('#patient_id').append(newOpt).trigger('change');

            $('#patient_phone').val(phone);
            $('#patient_age').val(age);
            $('#patient_sex').val(sex);
            $('#patient_bp').val(bp);

            $('#quickAddPatientModal').modal('hide');
        });

        // Quick Add Bank Modal
        $('#btnOpenAddBankModal').on('click', function() {
            $('#ajaxAddBankForm')[0].reset();
            $('#addBankModal').modal('show');
        });

        $('#btnSaveBankAjax').on('click', function() {
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
                        const newOption = new Option(b.bank_name + ' (' + b.account_number + ')', b.id, true, true);
                        $('#bank_id').append(newOption).trigger('change');
                        $('#addBankModal').modal('hide');
                        $('#ajaxAddBankForm')[0].reset();
                    } else {
                        alert(res.message || 'Error saving bank account.');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Save Bank');
                    alert('Error saving bank account.');
                }
            });
        });

        // SUBMIT SALE UPDATE (AJAX)
        $('#btnUpdateSale').on('click', function() {
            if (selectedItems.length === 0) {
                Swal.fire('Error', 'Please add at least one medicine/product to update this sale.', 'error');
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
                _token: '{{ csrf_token() }}',
                patient_id: $('#patient_id').val() && !$('#patient_id').val().startsWith('NEW_') ? $('#patient_id').val() : null,
                patient_name: $('#patient_id').val() && $('#patient_id').val().startsWith('NEW_') ? $('#patient_id option:selected').text().split('(')[0].trim() : ($('#patient_id option:selected').text().split('(')[0].trim() || ''),
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
                        total: lineBase + lineGst
                    };
                }),
            };

            const btn = $('#btnUpdateSale');
            btn.prop('disabled', true).html('<span>Updating Sale...</span>');

            $.ajax({
                url: '{{ route("sales.update_payment", $order->id) }}',
                type: 'POST',
                data: payload,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                success: function(res) {
                    btn.prop('disabled', false).html('<span>Update Sale Bill <i class="fas fa-arrow-right ms-1"></i></span>');
                    if (res && res.status) {
                        Swal.fire({
                            title: 'Success!',
                            text: res.message || 'Sale bill updated successfully!',
                            icon: 'success',
                            confirmButtonColor: '#f89884',
                            confirmButtonText: 'View Invoice',
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            window.location.href = res.invoice_url || res.redirect || ('/sales-invoice/' + (res.order_id || '{{ $order->id }}'));
                        });
                    } else {
                        Swal.fire('Error', res.message || 'Error updating sale bill', 'error');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<span>Update Sale Bill <i class="fas fa-arrow-right ms-1"></i></span>');
                    const msg = xhr.responseJSON ? (xhr.responseJSON.message || 'Error updating sale bill') : 'Error updating sale bill';
                    Swal.fire('Error', msg, 'error');
                }
            });
        });
    });
</script>
@endsection
