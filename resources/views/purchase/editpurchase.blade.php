@extends('layout.app')

@section('title', 'Edit Purchase - #' . ($invoice->bill_no ?? $invoice->invoice_number))

@section('content')
<style>
    :root {
        --clinic-primary: #f89884;
        --clinic-primary-hover: #f6816a;
        --clinic-secondary: #cfece0;
        --clinic-dark: #1e293b;
        --clinic-slate: #5a6a80;
    }

    /* Unified Single Main Card */
    .purchase-main-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 24px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        margin-bottom: 30px;
    }

    /* Section Dividers with proper margin */
    .purchase-section-divider {
        height: 1px;
        background: #edf2f7;
        margin: 24px 0;
    }

    /* 50% Radius on all inputs, selects, and buttons */
    .form-control, .form-select, select.form-control,
    .select2-container .select2-selection--single {
        border-radius: 50px !important;
        height: 42px !important;
        border: 1.5px solid #cbd5e1 !important;
        padding: 6px 18px !important;
        font-size: 13.5px !important;
        background-color: #ffffff !important;
        color: #1e293b !important;
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #f89884 !important;
        box-shadow: 0 0 0 3px rgba(248, 152, 132, 0.25) !important;
        outline: none;
    }
    .form-control[readonly] {
        background-color: #f8fafc !important;
        color: #475569 !important;
    }
    textarea.form-control {
        height: auto !important;
        border-radius: 20px !important;
        padding: 12px 18px !important;
    }

    .select2-container .select2-selection--single {
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
        padding-left: 0 !important;
        color: #1e293b !important;
        font-size: 13.5px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 14px !important;
    }

    .form-group-compact {
        margin-bottom: 0;
    }
    .form-group-compact label {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }

    .pill-btn {
        border-radius: 50px !important;
        font-weight: 600;
        padding: 8px 24px;
        font-size: 13.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
        border: none;
    }

    .btn-clinic-primary {
        background-color: #f89884 !important;
        color: #ffffff !important;
    }
    .btn-clinic-primary:hover {
        background-color: #f6816a !important;
        color: #ffffff !important;
    }

    .btn-clinic-secondary {
        background-color: #cfece0 !important;
        color: #1f513f !important;
    }
    .btn-clinic-secondary:hover {
        background-color: #b8e2d1 !important;
        color: #143e30 !important;
    }

    /* Product Item Cards (Within the same main card) */
    .purchase-item-card {
        background: #fafbfc;
        border: 1.5px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px 22px;
        margin-bottom: 16px;
        position: relative;
        transition: all 0.2s ease;
    }
    .purchase-item-card:hover {
        border-color: #cbd5e1;
        background: #ffffff;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.03);
    }

    .btn-remove-row {
        width: 36px;
        height: 36px;
        border-radius: 50% !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid #fecaca;
        background: #fff5f5;
        color: #ef4444;
        cursor: pointer;
        transition: all 0.2s;
        padding: 0;
    }
    .btn-remove-row:hover {
        background: #ef4444;
        color: #ffffff;
        border-color: #ef4444;
    }

    /* Payment Mode Buttons */
    .pay-mode-btn {
        flex: 1 1 130px;
        min-height: 50px;
        border: 1.5px solid #cbd5e1;
        border-radius: 50px !important;
        background: #ffffff;
        font-size: 13.5px;
        font-weight: 600;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
        padding: 6px 18px;
    }
    .pay-mode-btn.active {
        background-color: #f89884 !important;
        border-color: #f89884 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(248, 152, 132, 0.35);
    }
    .pay-mode-btn:hover:not(.active) {
        border-color: #f89884;
        color: #f89884;
        background: #fff9f8;
    }

    /* Summary Panel */
    .summary-box {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 20px;
        padding: 20px 26px;
        min-width: 310px;
    }
    .summary-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        font-size: 14px;
        color: #334155;
    }
    .summary-line:last-child {
        margin-bottom: 0;
        border-top: 1.5px dashed #cbd5e1;
        padding-top: 12px;
    }

    /* Modal Subsections */
    .modal-section-title {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #cfece0;
        padding-bottom: 6px;
        margin-bottom: 14px;
        margin-top: 6px;
    }

    /* Modals */
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
    .page-wrapper {
        padding-top: 70px !important;
    }
    .page-wrapper > .content,
    .content {
        padding-top: 22px !important;
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
        <form id="purchaseEditForm" method="POST" action="{{ route('purchase.update', $invoice->id) }}">
            @csrf

            <!-- Header Row -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3 flex-wrap" style="gap:10px">
                    <h3 class="page-title mb-0" style="font-weight: 700; color: #1e293b; font-size: 21px;">
                        Edit Purchase <span style="color: #f89884;">#{{ $invoice->bill_no ?? $invoice->invoice_number }}</span>
                    </h3>
                    <div class="d-inline-flex align-items-center gap-3 border-start ps-3">
                        <div class="form-check form-check-inline mb-0">
                            <input class="form-check-input" type="radio" name="gst_option" id="radioNoGst" value="without_gst" {{ ($invoice->gst_option === 'with_gst') ? '' : 'checked' }}>
                            <label class="form-check-label fw-semibold" for="radioNoGst" style="font-size: 13.5px;">Without GST</label>
                        </div>
                        <div class="form-check form-check-inline mb-0">
                            <input class="form-check-input" type="radio" name="gst_option" id="radioWithGst" value="with_gst" {{ ($invoice->gst_option === 'with_gst') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="radioWithGst" style="font-size: 13.5px;">With GST</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2" style="gap:10px">
                    <a href="{{ route('purchase.invoice', $invoice->id) }}" class="btn btn-clinic-secondary pill-btn">
                        <i class="fas fa-file-invoice"></i> View Invoice
                    </a>
                    <a href="{{ route('purchase.lists') }}" class="btn pill-btn btn-secondary" style="border-radius: 50px !important;">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <!-- UNIFIED ALL-IN-ONE CARD CONTAINER -->
            <div class="card purchase-main-card">
                <div class="card-body p-4 p-md-5">

                    <!-- 1. TOP VENDOR & BILL INFO -->
                    <div class="row g-3">
                        <!-- Vendor Name -->
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="form-group-compact">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="mb-0">Vendor Name <span class="text-danger">*</span></label>
                                    <button type="button" class="btn btn-sm pill-btn btn-clinic-primary" style="padding: 2px 14px; font-size: 11.5px;" data-bs-toggle="modal" data-bs-target="#quickAddVendorModal">
                                        + Add 
                                    </button>
                                </div>
                                <select name="vendor_id" id="vendor_name" class="form-control select2" required>
                                    <option value="">Select Vendor</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" data-phone="{{ $vendor->phone }}" {{ ($invoice->vendor_id == $vendor->id) ? 'selected' : '' }}>
                                            {{ $vendor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="form-group-compact">
                                <label>Date <span class="text-danger">*</span></label>
                                @php
                                    $pDateVal = $invoice->purchase_date ? date('Y-m-d', strtotime($invoice->purchase_date)) : ($invoice->created_at ? $invoice->created_at->format('Y-m-d') : date('Y-m-d'));
                                @endphp
                                <input type="date" name="purchase_date" id="purchase_date" class="form-control" value="{{ $pDateVal }}" required>
                            </div>
                        </div>

                        <!-- Bill No -->
                        <div class="col-lg-4 col-md-12 col-12">
                            <div class="form-group-compact">
                                <label>Bill No <span class="text-danger">*</span></label>
                                <input type="text" name="bill_no" id="bill_no" class="form-control" placeholder="Enter Bill Number" value="{{ $invoice->bill_no ?? $invoice->invoice_number }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons for Items -->
                    <div class="d-flex justify-content-end align-items-center gap-2 mt-4 mb-3" style="gap:10px">
                        <button type="button" class="btn pill-btn btn-clinic-primary" id="openAddMedicineModalBtn" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
                            <i class="fas fa-plus"></i> Add Medicine
                        </button>
                        <button type="button" class="btn pill-btn" id="btnAddRow" style="background-color: #28c76f; color: #ffffff; border: none;">
                            <i class="fas fa-plus-circle"></i> Add Row
                        </button>
                    </div>

                    <!-- 2. PURCHASE ITEMS CONTAINER -->
                    <div id="purchaseItemsContainer">
                        @php
                            $purchasesList = $invoice->purchases;
                        @endphp

                        @forelse($purchasesList as $item)
                            <div class="purchase-item-card">
                                <!-- Sub-Row 1 -->
                                <div class="row g-3 mb-3">
                                    <div class="col-lg-2 col-md-4 col-6">
                                        <div class="form-group-compact">
                                            <label>Category Name</label>
                                            <select name="category_name[]" class="form-control select2 category-select">
                                                <option value="">Category Name</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}" {{ ($item->medicine && $item->medicine->category_id == $cat->id) ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-6">
                                        <div class="form-group-compact">
                                            <label>Product Name <span class="text-danger">*</span></label>
                                            <select name="product_name[]" class="form-control select2 product-select" required>
                                                <option value="">Type product name</option>
                                                @foreach($medicines as $med)
                                                    <option value="{{ $med->id }}"
                                                            data-price="{{ $med->purchase_price ?? $med->price ?? 0 }}"
                                                            data-mrp="{{ $med->mrp ?? 0 }}"
                                                            data-category="{{ $med->category_id }}"
                                                            data-brand="{{ $med->brand_id }}"
                                                            data-batch="{{ $med->batch_no }}"
                                                            {{ ($item->item == $med->id || ($item->medicine && $item->medicine->id == $med->id)) ? 'selected' : '' }}>
                                                        {{ $med->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-6">
                                        <div class="form-group-compact">
                                            <label>Batch No</label>
                                            <input type="text" name="batch_no[]" class="form-control batch-input" placeholder="Batch No" value="{{ $item->batch_no }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-6">
                                        <div class="form-group-compact">
                                            <label>Type</label>
                                            <input type="text" name="purchase_type[]" class="form-control purchase-type-input" value="{{ $item->purchase_type ?? 'Local' }}" placeholder="Local / Other">
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-md-4 col-6">
                                        <div class="form-group-compact">
                                            <label>Qty</label>
                                            <input type="number" name="quantity[]" class="form-control quantity-input" value="{{ $item->quantity ?? 1 }}" style="padding:10px !important"  min="1" step="1">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-6">
                                        <div class="form-group-compact">
                                            <label>Purchase Rate</label>
                                            <input type="number" step="0.01" min="0" name="price[]" class="form-control rate-input" placeholder="0.00" value="{{ number_format($item->price ?? 0, 2, '.', '') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Sub-Row 2 -->
                                <div class="row g-3 align-items-end">
                                    <div class="col-lg-2 col-md-3 col-6">
                                        <div class="form-group-compact">
                                            <label>Discount (%)</label>
                                            <input type="number" step="0.01" min="0" max="100" name="discount_percent[]" class="form-control discount-percent-input" value="{{ $item->discount_percent ?? 0 }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-3 col-6">
                                        <div class="form-group-compact">
                                            <label>Discount Price</label>
                                            <input type="number" step="0.01" min="0" name="discount_price[]" class="form-control discount-price-input" value="{{ number_format($item->discount_amount ?? 0, 2, '.', '') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-6">
                                        <div class="form-group-compact">
                                            <label>Brand Name</label>
                                            <select name="brand_name[]" class="form-control select2 brand-select">
                                                <option value="">Select Brand</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand->name }}" {{ ($item->brand_id == $brand->id || $item->brand_id == $brand->name) ? 'selected' : '' }}>
                                                        {{ $brand->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-3 col-6">
                                        <div class="form-group-compact">
                                            <label>Expiry Date</label>
                                            <input type="date" name="exp_date[]" class="form-control expiry-input" value="{{ $item->exp_date ? date('Y-m-d', strtotime($item->exp_date)) : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-8">
                                        <div class="form-group-compact">
                                            <label>Total</label>
                                            <input type="text" name="total[]" class="form-control row-total-input" value="{{ number_format($item->amount_total ?? 0, 2, '.', '') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-md-2 col-4 text-center pb-1">
                                        <button type="button" class="btn-remove-row" title="Remove Item">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="purchase-item-card">
                                <div class="row g-3 mb-3">
                                    <div class="col-lg-2 col-md-4 col-6">
                                        <div class="form-group-compact">
                                            <label>Category Name</label>
                                            <select name="category_name[]" class="form-control select2 category-select">
                                                <option value="">Category Name</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-6">
                                        <div class="form-group-compact">
                                            <label>Product Name <span class="text-danger">*</span></label>
                                            <select name="product_name[]" class="form-control select2 product-select" required>
                                                <option value="">Type product name</option>
                                                @foreach($medicines as $med)
                                                    <option value="{{ $med->id }}" data-price="{{ $med->purchase_price ?? 0 }}">{{ $med->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-6">
                                        <div class="form-group-compact">
                                            <label>Batch No</label>
                                            <input type="text" name="batch_no[]" class="form-control batch-input" placeholder="Batch No">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-6">
                                        <div class="form-group-compact">
                                            <label>Purchase Type</label>
                                            <input type="text" name="purchase_type[]" class="form-control purchase-type-input" value="Local">
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-md-4 col-6">
                                        <div class="form-group-compact">
                                            <label>Qty</label>
                                            <input type="number" name="quantity[]" class="form-control quantity-input" value="1" min="1">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-6">
                                        <div class="form-group-compact">
                                            <label>Purchase Rate</label>
                                            <input type="number" step="0.01" min="0" name="price[]" class="form-control rate-input" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 align-items-end">
                                    <div class="col-lg-2 col-md-3 col-6">
                                        <div class="form-group-compact">
                                            <label>Discount (%)</label>
                                            <input type="number" step="0.01" min="0" max="100" name="discount_percent[]" class="form-control discount-percent-input" value="0">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-3 col-6">
                                        <div class="form-group-compact">
                                            <label>Discount Price</label>
                                            <input type="number" step="0.01" min="0" name="discount_price[]" class="form-control discount-price-input" value="0.00">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-6">
                                        <div class="form-group-compact">
                                            <label>Brand Name</label>
                                            <select name="brand_name[]" class="form-control select2 brand-select">
                                                <option value="">Select Brand</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand->name }}">{{ $brand->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-3 col-6">
                                        <div class="form-group-compact">
                                            <label>Expiry Date</label>
                                            <input type="date" name="exp_date[]" class="form-control expiry-input">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-8">
                                        <div class="form-group-compact">
                                            <label>Total</label>
                                            <input type="text" name="total[]" class="form-control row-total-input" value="0.00" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-md-2 col-4 text-center pb-1">
                                        <button type="button" class="btn-remove-row" title="Remove Item"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- <div class="purchase-section-divider"></div> -->

                    <!-- 3. SHIPPING & DUE DATE DETAILS -->
                    <div class="row g-3">
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="form-group-compact">
                                <label>Shipping</label>
                                <input type="number" step="0.01" min="0" name="shipping" id="shipping" class="form-control" value="{{ number_format($invoice->shipping ?? 0, 2, '.', '') }}">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="form-group-compact">
                                <label>Payment Due Days</label>
                                <input type="number" min="0" name="payment_due_days" id="payment_due_days" class="form-control" placeholder="Days" value="{{ $invoice->payment_due_days }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-6">
                            <div class="form-group-compact">
                                <label>Interest % (Before Due)</label>
                                <input type="number" step="0.01" min="0" name="interest_before_due" id="interest_before_due" class="form-control" placeholder="0.00" value="{{ $invoice->interest_type }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-6">
                            <div class="form-group-compact">
                                <label>Interest % (After Due)</label>
                                <input type="number" step="0.01" min="0" name="interest_after_due" id="interest_after_due" class="form-control" placeholder="0.00" value="{{ $invoice->interest_amount }}">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <div class="form-group-compact">
                                <label>Due Date</label>
                                <input type="date" name="due_date" id="due_date" class="form-control" value="{{ $invoice->due_date ? date('Y-m-d', strtotime($invoice->due_date)) : '' }}">
                            </div>
                        </div>
                    </div>

                    <div class="purchase-section-divider"></div>

                    <!-- 4. PAYMENT MODE SECTION -->
                    <div class="mb-2">
                        <label class="fw-bold mb-3 text-dark" style="font-size: 14px;">
                            <i class="fas fa-wallet me-1" style="color: #f89884;"></i> Payment Mode
                        </label>

                        @php
                            $initPaid = (float)($invoice->paid ?? 0);
                            $initRem = (float)($invoice->remaining_amount ?? 0);
                            $isPayLater = ($initPaid <= 0 && $invoice->status === 'pending');
                        @endphp
                        <input type="hidden" name="payment_mode" id="payment_mode" value="{{ $isPayLater ? 'pay_later' : 'cash' }}">

                        <div class="d-flex flex-wrap gap-2 mb-3" style="gap:10px">
                            <button type="button" class="pay-mode-btn {{ $isPayLater ? 'active' : '' }}" data-mode="pay_later">
                                <i class="far fa-clock"></i> Pay Later
                            </button>
                            <button type="button" class="pay-mode-btn {{ !$isPayLater ? 'active' : '' }}" data-mode="cash">
                                <i class="fas fa-money-bill-wave"></i> Cash
                            </button>
                            <button type="button" class="pay-mode-btn" data-mode="online">
                                <i class="fas fa-credit-card"></i> Online / UPI
                            </button>
                            <button type="button" class="pay-mode-btn" data-mode="cash_online">
                                <i class="fas fa-layer-group"></i> Cash + Online
                            </button>
                        </div>

                        <!-- Dynamic Sub-Fields for Payment -->
                        <div id="paymentFieldsContainer">
                            <div class="row g-3 align-items-center">
                                <!-- Paid Type -->
                                <div class="col-lg-3 col-md-4 col-12 {{ $isPayLater ? 'd-none' : '' }}" id="paidTypeCol">
                                    <div class="form-group-compact">
                                        <label>Paid Type</label>
                                        <select name="paid_type" id="paid_type" class="form-control select2">
                                            <option value="full" {{ ($initRem <= 0) ? 'selected' : '' }}>Fully Paid</option>
                                            <option value="partial" {{ ($initRem > 0 && $initPaid > 0) ? 'selected' : '' }}>Partially Paid</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Single Amount Input (Cash or Online) -->
                                <div class="col-lg-3 col-md-4 col-6 {{ $isPayLater ? 'd-none' : '' }}" id="enterAmountCol">
                                    <div class="form-group-compact">
                                        <label>Amount</label>
                                        <input type="number" step="0.01" min="0" name="enter_amount" id="enter_amount" class="form-control" value="{{ number_format($initPaid, 2, '.', '') }}">
                                    </div>
                                </div>

                                <!-- Cash + Online Amounts -->
                                <div class="col-lg-3 col-md-4 col-6 d-none" id="cashAmountCol">
                                    <div class="form-group-compact">
                                        <label>Cash Amount</label>
                                        <input type="number" step="0.01" min="0" name="cash_amount" id="cash_amount" class="form-control" value="0.00">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-6 d-none" id="onlineAmountCol">
                                    <div class="form-group-compact">
                                        <label>Online Amount</label>
                                        <input type="number" step="0.01" min="0" name="online_amount" id="online_amount" class="form-control" value="0.00">
                                    </div>
                                </div>

                                <!-- Pending Amount -->
                                <div class="col-lg-3 col-md-4 col-6" id="pendingAmountCol">
                                    <div class="form-group-compact">
                                        <label>Pending Amount</label>
                                        <input type="text" name="pending_amount" id="pending_amount" class="form-control" value="{{ number_format($initRem, 2, '.', '') }}" readonly>
                                    </div>
                                </div>

                                <!-- Bank Select -->
                                <div class="col-lg-3 col-md-6 col-12 d-none" id="bankSelectCol">
                                    <div class="form-group-compact">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="mb-0">Select Bank</label>
                                            <button type="button" class="btn btn-sm pill-btn btn-clinic-primary" style="padding: 1px 12px; font-size: 11.5px;" data-bs-toggle="modal" data-bs-target="#addBankModal">+ Add Bank</button>
                                        </div>
                                        <select name="bank_id" id="bank_id" class="form-control select2">
                                            <option value="">Select Bank</option>
                                            @foreach($banks as $b)
                                                <option value="{{ $b->id }}">{{ $b->bank_name ?? $b->name }} ({{ $b->account_number }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="purchase-section-divider"></div>

                    <!-- 5. BOTTOM SUMMARY & ACTIONS ROW -->
                    <div class="row align-items-center g-3">
                        <div class="col-lg-6 col-12">
                            <div class="d-flex align-items-center gap-3" style="gap:10px">
                                <button type="submit" class="btn pill-btn btn-clinic-primary px-5 py-2" id="btnUpdatePurchase">
                                    <i class="fas fa-save me-1"></i> Update Purchase
                                </button>
                                <a href="{{ route('purchase.lists') }}" class="btn pill-btn btn-secondary px-4 py-2" style="border-radius: 50px !important;">
                                    Cancel
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-6 col-12 d-flex justify-content-lg-end">
                            <div class="summary-box">
                                <div class="summary-line">
                                    <span class="text-muted fw-semibold">Total Product Amount</span>
                                    <span class="fw-bold text-success" id="summarySubtotal">₹{{ number_format($invoice->total_amount ?? 0, 2) }}</span>
                                </div>
                                <div class="summary-line">
                                    <span class="text-muted fw-semibold">Grand Total</span>
                                    <span class="fw-bold text-success" id="summaryGrandTotal">₹{{ number_format($invoice->grand_total ?? 0, 2) }}</span>
                                </div>
                                <div class="summary-line">
                                    <span class="fw-bold text-dark">Final Payable Amount</span>
                                    <span class="fw-bold text-success fs-5" id="summaryFinalPayable">₹{{ number_format($invoice->final_amount_after_interest ?? $invoice->grand_total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <input type="hidden" name="grand_total" id="hidden_grand_total" value="{{ $invoice->grand_total ?? 0.00 }}">
            <input type="hidden" name="subtotal" id="hidden_subtotal" value="{{ $invoice->total_amount ?? 0.00 }}">
        </form>
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- 1. FULL VENDOR DETAILS MODAL (Matching Supplier Create Module) -->
<div class="modal fade" id="quickAddVendorModal" tabindex="-1" aria-labelledby="quickAddVendorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="quickAddVendorForm">
                @csrf
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title font-weight-bold" id="quickAddVendorModalLabel" style="color: #ffffff; font-size: 18px; margin: 0;">
                        <i class="fas fa-building me-1"></i> Add Vendor
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Basic Vendor Information -->
                    <div class="modal-section-title"><i class="fas fa-building me-2" style="color: #f89884;"></i> Basic Vendor Information</div>
                    <div class="row g-3 mb-3">
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Company / Vendor Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="newVendorName" placeholder="Enter company name" required>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Contact Person <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="contact_person" id="newVendorContactPerson" placeholder="Enter contact person" required>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="phone" id="newVendorPhone" placeholder="Enter phone number" required>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Email</label>
                            <input type="email" class="form-control" name="email" id="newVendorEmail" placeholder="Enter email address">
                        </div>
                    </div>

                    <!-- Tax & Drug License Details -->
                    <div class="modal-section-title"><i class="fas fa-file-invoice me-2" style="color: #f89884;"></i> Tax & Drug License Details</div>
                    <div class="row g-3 mb-3">
                        <div class="col-lg-4 col-md-4 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">GSTIN</label>
                            <input type="text" class="form-control" name="gst_number" id="newVendorGst" placeholder="Enter GST number">
                        </div>
                        <div class="col-lg-4 col-md-4 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Drug License No (DL No)</label>
                            <input type="text" class="form-control" name="drug_license_no" id="newVendorDl" placeholder="Enter DL number">
                        </div>
                        <div class="col-lg-4 col-md-4 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">PAN No</label>
                            <input type="text" class="form-control" name="pan_no" id="newVendorPan" placeholder="Enter PAN number">
                        </div>
                    </div>

                    <!-- Bank & Ledger Accounts -->
                    <div class="modal-section-title"><i class="fas fa-university me-2" style="color: #f89884;"></i> Bank & Ledger Accounts</div>
                    <div class="row g-3 mb-3">
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Bank Name</label>
                            <input type="text" class="form-control" name="bank_name" id="newVendorBankName" placeholder="e.g. HDFC Bank">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Account Number</label>
                            <input type="text" class="form-control" name="account_no" id="newVendorAccountNo" placeholder="Enter account number">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">IFSC Code</label>
                            <input type="text" class="form-control" name="ifsc_code" id="newVendorIfsc" placeholder="Enter IFSC code">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Opening Balance (₹)</label>
                            <input type="number" step="0.01" class="form-control" name="opening_balance" id="newVendorOpeningBalance" value="0.00">
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="row g-3 mb-2">
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="address" id="newVendorAddress" rows="2" placeholder="Enter complete vendor address" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn pill-btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn pill-btn btn-clinic-primary" id="btnSaveNewVendor">Save Vendor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. ADD MEDICINE MODAL (Matching Sales Modal Structure & Styling) -->
<div class="modal fade" id="addMedicineModal" tabindex="-1" aria-labelledby="addMedicineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title font-weight-bold" id="addMedicineModalLabel" style="color: #ffffff; font-size: 18px; margin: 0;">
                    <i class="fas fa-pills me-1"></i> Add Medicine
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addMedicineForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Row 1: Product Type, Category, Medicine Name, Brand -->
                    <div class="row g-3 mb-3">
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Product Type</label>
                            <select class="form-control select2" name="service_type" id="modal_service_type">
                                <option value="Medicine" selected>Medicine</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Category <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="category_id" id="modal_category_id" required>
                                <option value="">Choose category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Medicine Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="modal_name" required placeholder="Medicine name">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Brand</label>
                            <select class="form-control select2" name="brand_id" id="modal_brand_id">
                                <option value="">No brand</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Stock, Purchase Price, MRP, GST Option -->
                    <div class="row g-3 mb-3">
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="quantity" id="modal_quantity" value="1" min="0" required>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Purchase Price (₹)</label>
                            <input type="number" step="0.01" class="form-control" name="purchase_price" id="modal_purchase_price" placeholder="0.00">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">MRP (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="rate" id="modal_mrp" required placeholder="0.00">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">GST Option</label>
                            <select class="form-control select2" name="gst_option" id="modal_gst_option">
                                <option value="without_gst" selected>Without GST</option>
                                <option value="with_gst">With GST</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: Batch No, Expiry Date -->
                    <div class="row g-3 mb-2">
                        <div class="col-lg-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Batch Number</label>
                            <input type="text" class="form-control" name="batch_no" id="modal_batch_no" placeholder="Batch No">
                        </div>
                        <div class="col-lg-6 col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Expiry Date</label>
                            <input type="date" class="form-control" name="expiry_date" id="modal_expiry_date">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn pill-btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn pill-btn btn-clinic-primary" id="saveMedicineBtn">Save Medicine</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. Quick Add Bank Modal -->
<div class="modal fade" id="addBankModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="quickAddBankForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-university me-1"></i> Quick Add Bank Account</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                    <button type="submit" class="btn pill-btn btn-clinic-primary" id="btnSaveNewBank">Save Bank</button>
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

    // -------------------------------------------------------------
    // Initialize Select2 Everywhere
    // -------------------------------------------------------------
    function initSelect2(context) {
        var $ctx = context ? $(context) : $(document);
        $ctx.find('.select2').select2({ width: '100%' });
    }
    initSelect2();

    // Explicit Modal Dismiss handler (ensures BS4/BS5 close & cancel work smoothly)
    $(document).on('click', '[data-dismiss="modal"], [data-bs-dismiss="modal"], .modal .close', function(e) {
        $(this).closest('.modal').modal('hide');
    });

    // Select2 inside Modals
    $('#addMedicineModal').on('shown.bs.modal', function() {
        $('#addMedicineModal .select2').select2({
            dropdownParent: $('#addMedicineModal'),
            width: '100%'
        });
    });

    $('#quickAddVendorModal').on('shown.bs.modal', function() {
        $('#quickAddVendorModal .select2').select2({
            dropdownParent: $('#quickAddVendorModal'),
            width: '100%'
        });
    });

    // -------------------------------------------------------------
    // Auto-calculate Due Date based on Date + Payment Due Days
    // -------------------------------------------------------------
    function updateDueDate() {
        var pDateStr = $('#purchase_date').val();
        var days = parseInt($('#payment_due_days').val()) || 0;
        if (pDateStr) {
            var pDate = new Date(pDateStr);
            pDate.setDate(pDate.getDate() + days);
            var yyyy = pDate.getFullYear();
            var mm = String(pDate.getMonth() + 1).padStart(2, '0');
            var dd = String(pDate.getDate()).padStart(2, '0');
            $('#due_date').val(yyyy + '-' + mm + '-' + dd);
        }
    }
    $('#purchase_date, #payment_due_days').on('change input', updateDueDate);

    // -------------------------------------------------------------
    // Item Calculation Logic
    // -------------------------------------------------------------
    function recalculateRow($card) {
        var qty = parseFloat($card.find('.quantity-input').val()) || 0;
        var rate = parseFloat($card.find('.rate-input').val()) || 0;
        var discPercent = parseFloat($card.find('.discount-percent-input').val()) || 0;

        var sub = qty * rate;
        var discAmt = sub * (discPercent / 100);
        $card.find('.discount-price-input').val(discAmt.toFixed(2));

        var rowTotal = Math.max(0, sub - discAmt);
        $card.find('.row-total-input').val(rowTotal.toFixed(2));

        calculateAllTotals();
    }

    function calculateAllTotals() {
        var subtotal = 0;
        $('.row-total-input').each(function() {
            subtotal += parseFloat($(this).val()) || 0;
        });

        var shipping = parseFloat($('#shipping').val()) || 0;
        var grandTotal = subtotal + shipping;

        $('#summarySubtotal').text('₹' + subtotal.toFixed(2));
        $('#summaryGrandTotal').text('₹' + grandTotal.toFixed(2));
        $('#summaryFinalPayable').text('₹' + grandTotal.toFixed(2));

        $('#hidden_subtotal').val(subtotal.toFixed(2));
        $('#hidden_grand_total').val(grandTotal.toFixed(2));

        updatePaymentPending();
    }

    $(document).on('input', '.quantity-input, .rate-input', function() {
        recalculateRow($(this).closest('.purchase-item-card'));
    });

    $(document).on('input', '.discount-percent-input', function() {
        var $card = $(this).closest('.purchase-item-card');
        var qty = parseFloat($card.find('.quantity-input').val()) || 0;
        var rate = parseFloat($card.find('.rate-input').val()) || 0;
        var discPercent = parseFloat($(this).val()) || 0;
        var sub = qty * rate;
        var discAmt = sub * (discPercent / 100);
        $card.find('.discount-price-input').val(discAmt.toFixed(2));

        var rowTotal = Math.max(0, sub - discAmt);
        $card.find('.row-total-input').val(rowTotal.toFixed(2));
        calculateAllTotals();
    });

    $(document).on('input', '.discount-price-input', function() {
        var $card = $(this).closest('.purchase-item-card');
        var qty = parseFloat($card.find('.quantity-input').val()) || 0;
        var rate = parseFloat($card.find('.rate-input').val()) || 0;
        var discAmt = parseFloat($(this).val()) || 0;
        var sub = qty * rate;

        if (sub > 0) {
            var discPercent = (discAmt / sub) * 100;
            $card.find('.discount-percent-input').val(discPercent.toFixed(2));
        }
        var rowTotal = Math.max(0, sub - discAmt);
        $card.find('.row-total-input').val(rowTotal.toFixed(2));
        calculateAllTotals();
    });

    $('#shipping').on('input', calculateAllTotals);

    // Auto-fill rate, batch, category on medicine select
    $(document).on('change', '.product-select', function() {
        var $opt = $(this).find('option:selected');
        var $card = $(this).closest('.purchase-item-card');
        if ($opt.val()) {
            var price = $opt.data('price') || 0;
            var batch = $opt.data('batch') || '';
            var cat = $opt.data('category') || '';

            if (price > 0 && (!$card.find('.rate-input').val() || $card.find('.rate-input').val() == '0.00')) {
                $card.find('.rate-input').val(parseFloat(price).toFixed(2));
            }
            if (batch && !$card.find('.batch-input').val()) {
                $card.find('.batch-input').val(batch);
            }
            if (cat && !$card.find('.category-select').val()) {
                $card.find('.category-select').val(cat).trigger('change');
            }
            recalculateRow($card);
        }
    });

    // -------------------------------------------------------------
    // Add / Remove Row Handling with Select2 re-init
    // -------------------------------------------------------------
    $('#btnAddRow').on('click', function() {
        var $firstCard = $('.purchase-item-card').first();
        var $clone = $firstCard.clone();

        $clone.find('.select2-container').remove();
        $clone.find('.select2').removeClass('select2-hidden-accessible').removeAttr('data-select2-id');

        $clone.find('input').val('');
        $clone.find('.quantity-input').val('1');
        $clone.find('.discount-percent-input').val('0');
        $clone.find('.discount-price-input').val('0.00');
        $clone.find('.row-total-input').val('0.00');
        $clone.find('.purchase-type-input').val('Local');
        $clone.find('select').prop('selectedIndex', 0);

        $('#purchaseItemsContainer').append($clone);
        $clone.find('.select2').select2({ width: '100%' });
    });

    $(document).on('click', '.btn-remove-row', function() {
        if ($('.purchase-item-card').length > 1) {
            $(this).closest('.purchase-item-card').remove();
            calculateAllTotals();
        } else {
            Swal.fire('Info', 'At least one purchase item is required.', 'info');
        }
    });

    // -------------------------------------------------------------
    // Payment Mode Switching
    // -------------------------------------------------------------
    $('.pay-mode-btn').on('click', function() {
        $('.pay-mode-btn').removeClass('active');
        $(this).addClass('active');

        var mode = $(this).data('mode');
        $('#payment_mode').val(mode);

        if (mode === 'pay_later') {
            $('#paidTypeCol, #enterAmountCol, #cashAmountCol, #onlineAmountCol, #bankSelectCol').addClass('d-none');
            $('#pendingAmountCol').removeClass('d-none');
            $('#enter_amount').val('0.00');
            $('#cash_amount').val('0.00');
            $('#online_amount').val('0.00');
        } else if (mode === 'cash_online') {
            $('#paidTypeCol, #cashAmountCol, #onlineAmountCol, #pendingAmountCol, #bankSelectCol').removeClass('d-none');
            $('#enterAmountCol').addClass('d-none');
        } else if (mode === 'online') {
            $('#paidTypeCol, #enterAmountCol, #pendingAmountCol, #bankSelectCol').removeClass('d-none');
            $('#cashAmountCol, #onlineAmountCol').addClass('d-none');
        } else { // cash
            $('#paidTypeCol, #enterAmountCol, #pendingAmountCol').removeClass('d-none');
            $('#cashAmountCol, #onlineAmountCol, #bankSelectCol').addClass('d-none');
        }

        updatePaymentPending();
    });

    $('#paid_type').on('change', function() {
        updatePaymentPending();
    });

    $('#enter_amount, #cash_amount, #online_amount').on('input', function() {
        updatePaymentPending();
    });

    function updatePaymentPending() {
        var grandTotal = parseFloat($('#hidden_grand_total').val()) || 0;
        var mode = $('#payment_mode').val();
        var paidType = $('#paid_type').val();

        if (mode === 'pay_later') {
            $('#pending_amount').val(grandTotal.toFixed(2));
            return;
        }

        if (mode === 'cash_online') {
            if (paidType === 'full') {
                var c = parseFloat($('#cash_amount').val()) || 0;
                var o = Math.max(0, grandTotal - c);
                $('#online_amount').val(o.toFixed(2));
                $('#pending_amount').val('0.00');
            } else {
                var c = parseFloat($('#cash_amount').val()) || 0;
                var o = parseFloat($('#online_amount').val()) || 0;
                var rem = Math.max(0, grandTotal - (c + o));
                $('#pending_amount').val(rem.toFixed(2));
            }
        } else {
            if (paidType === 'full') {
                $('#enter_amount').val(grandTotal.toFixed(2));
                $('#pending_amount').val('0.00');
            } else {
                var enter = parseFloat($('#enter_amount').val()) || 0;
                var rem = Math.max(0, grandTotal - enter);
                $('#pending_amount').val(rem.toFixed(2));
            }
        }
    }

    // -------------------------------------------------------------
    // Form Submit via AJAX (Update)
    // -------------------------------------------------------------
    $('#purchaseEditForm').on('submit', function(e) {
        e.preventDefault();

        var grandTotal = parseFloat($('#hidden_grand_total').val()) || 0;
        if (grandTotal <= 0) {
            Swal.fire('Warning', 'Please add items and enter prices to update purchase bill.', 'warning');
            return;
        }

        var $btn = $('#btnUpdatePurchase');
        var origText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Updating...').prop('disabled', true);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                $btn.html(origText).prop('disabled', false);
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        window.location.href = res.redirect || '{{ route("purchase.lists") }}';
                    });
                } else {
                    Swal.fire('Error', res.message || 'Unable to update purchase bill', 'error');
                }
            },
            error: function(xhr) {
                $btn.html(origText).prop('disabled', false);
                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Server error updating purchase';
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // -------------------------------------------------------------
    // FULL VENDOR FORM SUBMIT (Matching Supplier Create)
    // -------------------------------------------------------------
    $('#quickAddVendorForm').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btnSaveNewVendor');
        $btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: '{{ route("vendor.ajax_store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                $btn.prop('disabled', false).text('Save Vendor');
                if (res.status && res.vendor) {
                    var newOpt = new Option(res.vendor.name + ' (' + (res.vendor.phone || '') + ')', res.vendor.id, true, true);
                    $('#vendor_name').append(newOpt).trigger('change');
                    $('#quickAddVendorModal').modal('hide');
                    $('#quickAddVendorForm')[0].reset();
                    Swal.fire('Success', 'Vendor added successfully with full details!', 'success');
                } else {
                    Swal.fire('Error', res.message || 'Failed to add vendor', 'error');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text('Save Vendor');
                var msg = 'Validation error saving vendor';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // -------------------------------------------------------------
    // Add Medicine Modal (Same like Sales)
    // -------------------------------------------------------------
    $('#addMedicineForm').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#saveMedicineBtn');
        $btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: '{{ route("medicines.ajax_store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                $btn.prop('disabled', false).text('Save Medicine');
                if (res.status && (res.product || res.medicine)) {
                    var p = res.product || res.medicine;
                    var newOpt = new Option(p.name, p.id, true, true);
                    $(newOpt).attr('data-price', p.purchase_price || 0)
                             .attr('data-mrp', p.mrp || 0)
                             .attr('data-category', p.category_id || '')
                             .attr('data-brand', p.brand_id || '')
                             .attr('data-batch', p.batch_no || '');

                    // Append to all product selects and select in current or last row
                    $('.product-select').append(newOpt);
                    var $targetCard = $('.purchase-item-card').last();
                    $targetCard.find('.product-select').val(p.id).trigger('change');
                    if (p.purchase_price) {
                        $targetCard.find('.rate-input').val(parseFloat(p.purchase_price).toFixed(2));
                    }
                    if (p.batch_no) {
                        $targetCard.find('.batch-input').val(p.batch_no);
                    }
                    if (p.category_id) {
                        $targetCard.find('.category-select').val(p.category_id).trigger('change');
                    }
                    recalculateRow($targetCard);

                    $('#addMedicineModal').modal('hide');
                    $('#addMedicineForm')[0].reset();
                    Swal.fire('Success', 'Medicine created and added to bill!', 'success');
                } else {
                    Swal.fire('Error', res.message || 'Failed to add medicine', 'error');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text('Save Medicine');
                Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.message : 'Validation failed', 'error');
            }
        });
    });

    // -------------------------------------------------------------
    // Quick Add Bank
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
                    $('#bank_id').append(newOpt).trigger('change');
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

    // Initialize totals
    calculateAllTotals();
});
</script>
@endsection
