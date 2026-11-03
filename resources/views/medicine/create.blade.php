@extends('layout.app')

@section('content')
<style>
    /* Site Theme: #f89884 and #cfece0 with 50% pill radius on fields and buttons */
    .medicine-create-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        background: #ffffff;
    }
    .medicine-create-card .card-body {
        padding: 28px 32px;
    }

    /* Labels & Add Badges */
    .form-group {
        margin-bottom: 22px !important;
    }
    .form-group label {
        font-size: 14px;
        /* font-weight: 600; */
        color: #666666;
        margin-bottom: 8px;
        display: block;
    }
    .add-badge {
        background-color: #f89884 !important;
        color: #ffffff !important;
        font-size: 11px;
        padding: 3px 12px;
        border-radius: 50px !important;
        float: right;
        text-decoration: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .add-badge:hover {
        background-color: #e58774 !important;
        color: #ffffff !important;
    }

    /* Form Controls - 50% Pill Radius */
    .form-control {
        height: 42px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 50px !important;
        padding: 8px 18px !important;
        font-size: 14px !important;
        color: #666666 !important;
        background-color: #ffffff;
        box-shadow: none !important;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control:focus {
        border-color: #f89884 !important;
        box-shadow: 0 0 0 3px rgba(248, 152, 132, 0.25) !important;
    }
    textarea.form-control {
        height: auto !important;
        min-height: 90px;
        border-radius: 20px !important;
        padding: 12px 18px !important;
    }

    /* Select2 Alignment & 50% Radius */
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
        font-size: 14px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 14px !important;
    }
    .select2-container--default .select2-selection--multiple {
        min-height: 42px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 50px !important;
        padding: 4px 12px !important;
    }

    /* Custom Toggle Switch for Is Featured */
    .custom-toggle-wrap {
        display: flex;
        align-items: center;
        height: 42px;
        gap: 10px;
    }
    .switch-pill {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
        margin-bottom: 0;
    }
    .switch-pill input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider-round {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 50px !important;
    }
    .slider-round:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50% !important;
    }
    input:checked + .slider-round {
        background-color: #f89884 !important;
    }
    input:checked + .slider-round:before {
        transform: translateX(24px);
    }

    /* Barcode styling & 50% radius */
    #addBarcodeBtn {
        background-color: #cfece0 !important;
        color: #000000 !important;
        border: 1px solid #b5e4d2 !important;
        border-radius: 50px !important;
        padding: 0 20px !important;
        font-weight: 600;
        font-size: 13.5px;
        height: 42px;
        transition: all 0.2s;
        margin-left: 5px;
    }
    #addBarcodeBtn:hover {
        background-color: #bce7d6 !important;
    }
    #generateBarcodeBtn {
        background-color: #f89884 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 50px !important;
        padding: 0 20px !important;
        font-weight: 600;
        font-size: 13.5px;
        height: 42px;
        transition: all 0.2s;
        margin-left: 5px;
    }
    #generateBarcodeBtn:hover {
        background-color: #e58774 !important;
    }
    .btn-barcode-scan {
        border: 1.5px solid #f89884 !important;
        color: #f89884 !important;
        background: #ffffff !important;
        border-radius: 50px !important;
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-left: 5px;
        
    }
    .btn-barcode-scan:hover {
        background: #fff3f0 !important;
    }
    .barcode-chip {
        display: inline-flex;
        align-items: center;
        background-color: #cfece0;
        color: #1e293b;
        border: 1px solid #b5e4d2;
        border-radius: 50px !important;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 600;

    }
    .barcode-chip .remove-barcode {
        margin-left: 8px;
        font-size: 14px;
        color: #ef4444;
        cursor: pointer;
    }

    /* Image upload box */
    .image-upload-wrap {
        border: 2px dashed #f89884;
        border-radius: 20px !important;
        padding: 24px;
        text-align: center;
        background: #fffcfb;
        position: relative;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .image-upload-wrap:hover {
        border-color: #e58774;
        background: #fff5f3;
    }
    .image-upload-wrap input[type="file"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }
    .image-upload-wrap .upload-content {
        pointer-events: none;
    }
    .image-upload-wrap .upload-content i {
        font-size: 28px;
        color: #f89884;
        margin-bottom: 8px;
    }
    .image-upload-wrap .upload-content h4 {
        font-size: 13.5px;
        color: #64748b;
        margin: 0;
        font-weight: 500;
    }

    /* Packaging card */
    .ai-pack-card {
        background: #fbfdfc;
        border: 1.5px solid #cfece0;
        border-radius: 20px;
        padding: 16px 20px;
        margin-bottom: 16px;
    }
    .ai-pack-badge {
        padding: 4px 14px;
        border-radius: 50px !important;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    /* Buttons - 50% radius (pill) */
    .btn-submit-main {
        background-color: #f89884 !important;
        border-color: #f89884 !important;
        color: #ffffff !important;
        font-weight: 600;
        font-size: 14px;
        padding: 10px 32px !important;
        border-radius: 50px !important;
        box-shadow: 0 2px 6px rgba(248, 152, 132, 0.35);
        transition: all 0.2s;
    }
    .btn-submit-main:hover {
        background-color: #e58774 !important;
        border-color: #e58774 !important;
        color: #ffffff !important;
    }
    .btn-cancel-main {
        background-color: #cfece0 !important;
        border-color: #b5e4d2 !important;
        color: #000000 !important;
        font-weight: 600;
        font-size: 14px;
        padding: 10px 26px !important;
        border-radius: 50px !important;
        transition: all 0.2s;
    }
    .btn-cancel-main:hover {
        background-color: #bce7d6 !important;
        color: #000000 !important;
    }
    .btn-back-main {
        background-color: #cfece0 !important;
        border: 1px solid #b5e4d2 !important;
        color: #000000 !important;
        border-radius: 50px !important;
        font-weight: 600;
        font-size: 13.5px;
        padding: 7px 20px !important;
        transition: all 0.2s;
    }
    .btn-back-main:hover {
        background-color: #bce7d6 !important;
        color: #000000 !important;
    }
    .form-control::placeholder, .form-select::placeholder, textarea.form-control::placeholder {
        color: #94a3b8 !important;
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="page-title mb-0" style="font-weight: 700; font-size: 21px; color: #1e293b;">Add Medicine</h4>
            <a href="{{ route('medicine.index') }}" class="btn btn-back-main">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <!-- Main Card Form -->
        <div class="card medicine-create-card">
            <div class="card-body">
                <form id="medicineForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="service_type" id="service_type" value="Medicine">
                    <div class="row">
                        <!-- Row 1 -->
                        <div class="col-lg-4 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Category <span class="add-badge" id="quickAddCategoryBtn">Add</span></label>
                                <select class="form-control select2" name="category_id" id="category_id" required>
                                    <option value="">Select or Add Category</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 col-12">
                            <div class="form-group">
                                <label id="product_name_label">Medicine Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Enter medicine name" required>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 col-12" id="brand_container">
                            <div class="form-group">
                                <label>Brand <span class="add-badge" id="quickAddBrandBtn">Add</span></label>
                                <select class="form-control select2" name="brand_id" id="brand_id">
                                    <option value="">Select or Add Brand</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 2 -->
                        <div class="col-lg-3 col-sm-6 col-12" id="quantity_container">
                            <div class="form-group">
                                <label>Stock</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" min="0" step="1" value="0">
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12" id="low_stock_qty_container">
                            <div class="form-group">
                                <label>Low Stock Qty</label>
                                <input type="number" name="low_stock_qty" id="low_stock_qty" class="form-control" min="0" step="1" placeholder="Optional">
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12" id="stock_unit_container">
                            <div class="form-group">
                                <label>Stock Unit <span class="add-badge" id="quickAddUnitBtn">Add</span></label>
                                <select class="form-control select2" name="stock_unit_id" id="stock_unit_id">
                                    <option value="">Select or Add Stock Unit</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12" id="gst_option_container">
                            <div class="form-group">
                                <label>GST Option</label>
                                <select class="form-control" name="gst_option" id="gst_option">
                                    <option value="Without GST" selected>Without GST</option>
                                    <option value="With GST">With GST</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12" id="gst_dropdown_container" style="display: none;">
                            <div class="form-group">
                                <label>Medicine GST <span class="add-badge" id="quickAddTaxBtn">Add</span></label>
                                <select class="form-control select2" name="product_gst[]" id="product_gst" multiple>
                                    <option value="">Choose GST Rate</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 3 -->
                        <div class="col-lg-3 col-sm-6 col-12" id="rate_container">
                            <div class="form-group">
                                <label>MRP</label>
                                <input type="number" name="mrp" id="mrp" class="form-control" min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12" id="purchase_price_container">
                            <div class="form-group">
                                <label>Purchase Price</label>
                                <input type="number" name="purchase_price" id="purchase_price" class="form-control" min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12" id="stock_value_container">
                            <div class="form-group">
                                <label>Stock Value</label>
                                <input type="number" name="stock_value" id="stock_value" class="form-control" min="0" step="0.01" value="0.00" readonly style="background-color: #f8fafc;">
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 4 -->
                        <div class="col-lg-3 col-sm-6 col-12" id="availability_container">
                            <div class="form-group">
                                <label>Stock</label>
                                <select class="form-control" name="availablility" id="availablility" disabled style="background-color: #f8fafc;">
                                    <option value="out_stock" selected>Out Of Stock</option>
                                    <option value="in_stock">In Stock</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Is Featured</label>
                                <div class="custom-toggle-wrap">
                                    <label class="switch-pill">
                                        <input type="checkbox" name="is_featured" id="is_featured" value="1">
                                        <span class="slider-round"></span>
                                    </label>
                                    <span style="font-size: 13.5px; font-weight: 500; color: #1e293b;">Yes</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12" id="expiry_date_container">
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="date" name="expiry_date" id="expiry_date" class="form-control">
                            </div>
                        </div>

                        <div class="col-lg-3 d-none d-lg-block">
                            <!-- Column alignment spacer matching reference UI -->
                        </div>

                        <!-- Row 5: Barcode (6 cols), Pack (3 cols), HSN (3 cols) -->
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="form-group">
                                <label>Barcode</label>
                                <div class="d-flex gap-2">
                                    <input type="text" id="barcode_input" name="barcode" class="form-control" placeholder="Enter barcode" style="flex: 1;">
                                    <button class="btn" type="button" id="addBarcodeBtn">Add</button>
                                    <button class="btn" type="button" id="generateBarcodeBtn">Auto</button>
                                    <button class="btn btn-barcode-scan" type="button" id="scanBarcodeBtn" title="Scan Barcode"><i class="fas fa-camera"></i></button>
                                </div>
                                <div id="barcode_chips_container" class="d-flex flex-wrap gap-2 mt-2"></div>
                                <div id="hidden_barcodes_container"></div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12" id="pack_container">
                            <div class="form-group">
                                <label>Pack</label>
                                <input type="text" name="pack" id="pack" class="form-control" placeholder="e.g. 10*10 or Strip">
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12" id="hsn_code_container">
                            <div class="form-group">
                                <label id="hsn_code_label">HSN Code</label>
                                <input type="text" class="form-control" name="hsn_code" id="hsn_code" placeholder="e.g. 3004">
                            </div>
                        </div>

                        <!-- Pack conversion card (for multi pack e.g. 10*10) -->
                        <div class="col-12" id="pack_conversion_card" style="display: none;">
                            <div class="ai-pack-card">
                                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                    <span style="font-weight: 700; font-size: 13px; color: #1e293b; text-transform: uppercase;">
                                        <i class="fas fa-boxes text-warning me-1"></i> Packaging & Retail Stock Conversion
                                    </span>
                                    <span class="ai-pack-badge" id="pack_formula_badge" style="background: #dcfce7; color: #15803d; border: 1px solid #86efac;"></span>
                                </div>
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3 col-6">
                                        <label class="form-label mb-1" style="font-size: 12px; font-weight: 600;">Pricing Option</label>
                                        <select class="form-control" id="ui_price_basis">
                                            <option value="per_pack">Per Pack / Strip</option>
                                            <option value="per_tab">Per Tab / Unit</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <label class="form-label mb-1" style="font-size: 12px; font-weight: 600;">Bill Qty (Packs)</label>
                                        <input type="number" step="1" min="0" class="form-control" id="ui_billed_qty" placeholder="e.g. 10">
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <label class="form-label mb-1" style="font-size: 12px; font-weight: 600;">Units per pack</label>
                                        <input type="number" step="1" min="1" class="form-control" id="ui_pack_factor" value="10">
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <label class="form-label mb-1" style="font-size: 12px; font-weight: 700; color: #047857;">Total Units (Stock)</label>
                                        <input type="number" step="1" min="0" class="form-control" id="ui_stock_qty" style="font-weight: 700; background: #ecfdf5; color: #065f46;" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row 6: Description & Upload -->
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="description" id="description" rows="3" placeholder="Medicine details or usage notes"></textarea>
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="form-group">
                                <label id="product_image_label">Medicine Image</label>
                                <div class="image-upload-wrap">
                                    <input type="file" name="image" id="image" accept="image/*">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt d-block"></i>
                                        <h4>Drag and drop a file to upload or click</h4>
                                    </div>
                                </div>
                                <div id="image_name_preview" class="mt-2 text-success fw-bold" style="font-size: 13px;"></div>
                            </div>
                        </div>

                        <!-- Medicine Documentation (Optional) -->
                        <div class="col-lg-12 mt-2" id="product_documentation_container">
                            <div class="card mb-3" style="border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: none;">
                                <div class="card-header bg-white py-3" style="border-bottom: 1px solid #edf2f7; border-top-left-radius: 16px; border-top-right-radius: 16px;">
                                    <h6 class="mb-0 fw-bold" style="color: #334155; font-size: 15px;">Medicine Documentation (Optional)</h6>
                                </div>
                                <div class="card-body" id="documentationContainer" style="padding: 20px;">
                                    <div class="documentation-row border p-3" style="border-radius: 14px; background-color: #fbfdff;">
                                        <div class="row">
                                            <div class="col-md-4 mb-3 mb-md-0">
                                                <label class="form-label fw-bold" style="font-size: 13px; color: #475569;">Batch Number</label>
                                                <input type="text" name="documentations[0][batch_number]"
                                                    class="form-control" placeholder="Enter Batch Number"
                                                    style="border-radius: 50px !important;">
                                                <span class="error_documentations text-danger"></span>
                                            </div>
                                            <div class="col-md-4 mb-3 mb-md-0">
                                                <label class="form-label fw-bold" style="font-size: 13px; color: #475569;">Manufacturing Date</label>
                                                <input type="month" name="documentations[0][mfg_date]"
                                                    class="form-control"
                                                    style="border-radius: 50px !important;">
                                            </div>
                                            <div class="col-md-4 mb-0">
                                                <label class="form-label fw-bold" style="font-size: 13px; color: #475569;">Upload COA (PDF)</label>
                                                <input type="file" name="documentations[0][coa_pdfs][]"
                                                    class="form-control" accept="application/pdf" multiple
                                                    style="border-radius: 50px !important; padding: 6px 16px;">
                                                <small class="text-muted d-block mt-1" style="font-size: 12px;">Max size 15MB</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="row mt-3 pt-3 border-top">
                        <div class="col-12">
                            <button type="submit" class="btn btn-submit-main me-2" id="submitMedicineBtn">Submit</button>
                            <a href="{{ route('medicine.index') }}" class="btn btn-cancel-main">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reusable Quick Add Modal for Category / Brand / Unit -->
<div class="modal fade" id="quickAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <div class="modal-header d-flex justify-content-between align-items-center" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 16px 20px;">
                <h5 class="modal-title fw-bold mb-0" id="quickAddModalTitle" style="color: #1e293b; font-size: 16px;">Add New</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: transparent; border: none; font-size: 24px; font-weight: 400; line-height: 1; color: #64748b; cursor: pointer; padding: 0 4px; outline: none; opacity: 0.8; transition: opacity 0.2s;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <input type="hidden" id="quickAddType">
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size: 13.5px;">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="quickAddName" placeholder="Enter name">
                    <span class="text-danger error-quick-add d-block mt-1" style="font-size: 12px;"></span>
                </div>
                <div class="mb-2" id="quickAddDescGroup">
                    <label class="form-label fw-bold" style="font-size: 13.5px;">Description / Short Name</label>
                    <input type="text" class="form-control" id="quickAddDesc" placeholder="Optional details">
                </div>
            </div>
            <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 12px 20px;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal" style="background-color: #cfece0; border-color: #b5e4d2; color: #1e293b; padding: 7px 20px; border-radius: 50px; font-weight: 600;">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="saveQuickAddBtn" style="background-color: #f89884; border-color: #f89884; padding: 7px 24px; border-radius: 50px; font-weight: 600;">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('token');
        const branchId = localStorage.getItem('selectedBranchId');

        if ($.fn.select2) {
            $('.select2').select2({ width: '100%' });
        }

        // Image file name preview
        $('#image').on('change', function() {
            if (this.files && this.files[0]) {
                $('#image_name_preview').text('Selected: ' + this.files[0].name);
            }
        });

        // 1. Load Dropdowns
        function loadCategories(selectedId = null) {
            return $.ajax({
                url: '{{ url("api/categories") }}',
                type: 'GET',
                headers: { 'Authorization': 'Bearer ' + token },
                data: { branch_id: branchId },
                success: function(res) {
                    const list = res.categories || (Array.isArray(res) ? res : []);
                    let html = '<option value="">Select or Add Category</option>';
                    list.forEach(c => {
                        const isSel = selectedId && (String(c.id) === String(selectedId));
                        html += `<option value="${c.id}" ${isSel ? 'selected' : ''}>${c.name}</option>`;
                    });
                    $('#category_id').html(html);
                    if (selectedId) {
                        $('#category_id').val(selectedId);
                    }
                    $('#category_id').trigger('change');
                }
            });
        }

        function loadBrands(selectedId = null) {
            return $.ajax({
                url: '{{ url("api/brands") }}',
                type: 'GET',
                headers: { 'Authorization': 'Bearer ' + token },
                data: { branch_id: branchId },
                success: function(res) {
                    const list = res.brands || (Array.isArray(res) ? res : []);
                    let html = '<option value="">Select or Add Brand</option>';
                    list.forEach(b => {
                        const isSel = selectedId && (String(b.id) === String(selectedId));
                        html += `<option value="${b.id}" ${isSel ? 'selected' : ''}>${b.name}</option>`;
                    });
                    $('#brand_id').html(html);
                    if (selectedId) {
                        $('#brand_id').val(selectedId);
                    }
                    $('#brand_id').trigger('change');
                }
            });
        }

        function loadStockUnits(selectedId = null) {
            return $.ajax({
                url: '{{ url("api/stock-units") }}',
                type: 'GET',
                headers: { 'Authorization': 'Bearer ' + token },
                data: { branch_id: branchId },
                success: function(res) {
                    const list = res.stock_units || (Array.isArray(res) ? res : []);
                    let html = '<option value="">Select or Add Stock Unit</option>';
                    list.forEach(u => {
                        const isSel = selectedId && (String(u.id) === String(selectedId));
                        const label = u.short_name ? `${u.name} (${u.short_name})` : u.name;
                        html += `<option value="${u.id}" ${isSel ? 'selected' : ''}>${label}</option>`;
                    });
                    $('#stock_unit_id').html(html);
                    if (selectedId) {
                        $('#stock_unit_id').val(selectedId);
                    }
                    $('#stock_unit_id').trigger('change');
                }
            });
        }

        function loadTaxRates() {
            $.ajax({
                url: '{{ url("api/tax-rates") }}',
                type: 'GET',
                headers: { 'Authorization': 'Bearer ' + token },
                success: function(res) {
                    if (res.data) {
                        let html = '';
                        res.data.forEach(t => {
                            html += `<option value="${t.id}">${t.tax_name} (${t.tax_rate}%)</option>`;
                        });
                        $('#product_gst').html(html);
                    }
                }
            });
        }

        loadCategories();
        loadBrands();
        loadStockUnits();
        loadTaxRates();

        // 2. Quantity & Stock Value calculation
        function calculateStock() {
            const qty = parseFloat($('#quantity').val()) || 0;
            const purchasePrice = parseFloat($('#purchase_price').val()) || parseFloat($('#mrp').val()) || 0;
            const stockVal = (qty * purchasePrice).toFixed(2);
            $('#stock_value').val(stockVal);

            if (qty > 0) {
                $('#availablility').val('in_stock');
            } else {
                $('#availablility').val('out_stock');
            }
        }

        $('#quantity, #purchase_price, #mrp').on('input change', calculateStock);

        // 3. GST Option Toggle
        $('#gst_option').on('change', function() {
            if ($(this).val() === 'With GST') {
                $('#gst_dropdown_container').slideDown();
            } else {
                $('#gst_dropdown_container').slideUp();
            }
        });

        // 4. Barcode Handlers
        let barcodesList = [];

        function renderBarcodeChips() {
            let chipsHtml = '';
            let hiddenInputs = '';
            barcodesList.forEach((code, index) => {
                chipsHtml += `
                    <span class="barcode-chip">
                        ${code}
                        <span class="remove-barcode" data-index="${index}">&times;</span>
                    </span>
                `;
                hiddenInputs += `<input type="hidden" name="barcodes[]" value="${code}">`;
            });
            $('#barcode_chips_container').html(chipsHtml);
            $('#hidden_barcodes_container').html(hiddenInputs);
        }

        $('#addBarcodeBtn').on('click', function() {
            const val = $('#barcode_input').val().trim();
            if (val && !barcodesList.includes(val)) {
                barcodesList.push(val);
                renderBarcodeChips();
                $('#barcode_input').val('');
            }
        });

        $('#generateBarcodeBtn').on('click', function() {
            const randomCode = 'MED' + Math.floor(1000000000 + Math.random() * 9000000000);
            $('#barcode_input').val(randomCode);
            if (!barcodesList.includes(randomCode)) {
                barcodesList.push(randomCode);
                renderBarcodeChips();
            }
        });

        $(document).on('click', '.remove-barcode', function() {
            const index = $(this).data('index');
            barcodesList.splice(index, 1);
            renderBarcodeChips();
        });

        // 5. Pack Formula (e.g. 10*10)
        $('#pack').on('input', function() {
            const val = $(this).val().trim();
            if (val.includes('*')) {
                const parts = val.split('*');
                const factor = parseInt(parts[1]) || 10;
                $('#ui_pack_factor').val(factor);
                $('#pack_formula_badge').text(`1 Pack = ${factor} Units`);
                $('#pack_conversion_card').slideDown();
            } else {
                $('#pack_conversion_card').slideUp();
            }
        });

        $('#ui_billed_qty, #ui_pack_factor').on('input', function() {
            const billedPacks = parseFloat($('#ui_billed_qty').val()) || 0;
            const factor = parseFloat($('#ui_pack_factor').val()) || 1;
            const totalUnits = billedPacks * factor;
            $('#ui_stock_qty').val(totalUnits);
            $('#quantity').val(totalUnits).trigger('input');
        });

        // 6. Quick Add Modals
        $('#quickAddCategoryBtn').on('click', function() {
            $('#quickAddType').val('category');
            $('#quickAddModalTitle').text('Add Category');
            $('#quickAddName').val('');
            $('#quickAddDesc').val('');
            $('.error-quick-add').text('');
            $('#quickAddModal').modal('show');
        });

        $('#quickAddBrandBtn').on('click', function() {
            $('#quickAddType').val('brand');
            $('#quickAddModalTitle').text('Add Brand');
            $('#quickAddName').val('');
            $('#quickAddDesc').val('');
            $('.error-quick-add').text('');
            $('#quickAddModal').modal('show');
        });

        $('#quickAddUnitBtn').on('click', function() {
            $('#quickAddType').val('unit');
            $('#quickAddModalTitle').text('Add Stock Unit');
            $('#quickAddName').val('');
            $('#quickAddDesc').val('').attr('placeholder', 'Short code (e.g. TAB, BOX)');
            $('.error-quick-add').text('');
            $('#quickAddModal').modal('show');
        });

        $('#quickAddModal [data-dismiss="modal"], #quickAddModal [data-bs-dismiss="modal"]').on('click', function() {
            $('#quickAddModal').modal('hide');
        });

        $('#saveQuickAddBtn').on('click', function() {
            const type = $('#quickAddType').val();
            const name = $('#quickAddName').val().trim();
            const desc = $('#quickAddDesc').val().trim();

            if (!name) {
                $('.error-quick-add').text('Name is required');
                return;
            }

            let endpoint = '';
            let payload = { branch_id: branchId, name: name };

            if (type === 'category') {
                endpoint = '{{ url("api/categories") }}';
                payload.description = desc;
            } else if (type === 'brand') {
                endpoint = '{{ url("api/brands") }}';
                payload.description = desc;
            } else if (type === 'unit' || type === 'stock_unit') {
                endpoint = '{{ url("api/stock-units") }}';
                payload.short_name = desc || name;
            }

            const $btn = $(this);
            $btn.prop('disabled', true).text('Saving...');
            $('.error-quick-add').text('');

            $.ajax({
                url: endpoint,
                type: 'POST',
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
                data: payload,
                success: function(res) {
                    $btn.prop('disabled', false).text('Save');
                    $('#quickAddModal').modal('hide');

                    if (type === 'category') {
                        const newId = (res.category && res.category.id) ? res.category.id : res.id;
                        loadCategories(newId);
                    } else if (type === 'brand') {
                        const newId = (res.brand && res.brand.id) ? res.brand.id : res.id;
                        loadBrands(newId);
                    } else if (type === 'unit' || type === 'stock_unit') {
                        const newId = (res.stock_unit && res.stock_unit.id) ? res.stock_unit.id : res.id;
                        loadStockUnits(newId);
                    }
                },
                error: function(err) {
                    $btn.prop('disabled', false).text('Save');
                    let msg = 'Error adding item';
                    if (err.responseJSON) {
                        if (err.responseJSON.message) {
                            msg = err.responseJSON.message;
                        } else if (err.responseJSON.errors) {
                            msg = Object.values(err.responseJSON.errors).flat().join(', ');
                        }
                    }
                    $('.error-quick-add').text(msg);
                }
            });
        });

        // 7. Form Submission
        $('#medicineForm').on('submit', function(e) {
            e.preventDefault();

            const form = document.getElementById('medicineForm');
            const formData = new FormData(form);
            formData.append('branch_id', branchId || '');

            if (!formData.get('barcode') && barcodesList.length > 0) {
                formData.set('barcode', barcodesList[0]);
            }

            $('#submitMedicineBtn').prop('disabled', true).text('Saving...');

            $.ajax({
                url: '{{ url("api/medicines") }}',
                type: 'POST',
                headers: { 'Authorization': 'Bearer ' + token },
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Medicine added successfully!',
                            icon: 'success',
                            confirmButtonColor: '#f89884',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '{{ route("medicine.index") }}';
                        });
                    } else {
                        Swal.fire('Error', res.message || 'Failed to save', 'error');
                        $('#submitMedicineBtn').prop('disabled', false).text('Submit');
                    }
                },
                error: function(err) {
                    $('#submitMedicineBtn').prop('disabled', false).text('Submit');
                    let msg = 'Failed to create medicine.';
                    if (err.responseJSON && err.responseJSON.message) {
                        msg = err.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
