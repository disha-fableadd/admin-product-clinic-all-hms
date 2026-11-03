@extends('layout.app')

@section('content')
<style>
    /* Site Theme: #f89884 and #cfece0 with 50% pill radius on fields and buttons */
    .vendor-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        background: #ffffff;
    }
    .vendor-card .card-body {
        padding: 28px 32px;
    }
    .form-group {
        margin-bottom: 22px !important;
    }
    .form-group label {
        font-size: 13.5px;
        font-weight: 600;
        color: #212b36;
        margin-bottom: 8px;
        display: block;
    }
    .form-control {
        height: 42px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 50px !important;
        padding: 8px 18px !important;
        font-size: 14px !important;
        color: #000000 !important;
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
    .section-title {
        font-size: 13.5px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #cfece0;
        padding-bottom: 8px;
        margin-bottom: 18px;
        margin-top: 10px;
    }
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="page-title mb-0" style="font-weight: 700; font-size: 21px; color: #1e293b;">Add Vendor</h4>
            <a href="{{ route('supplier.index') }}" class="btn btn-back-main">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="card vendor-card">
            <div class="card-body">
                <form id="createSupplierForm">
                    @csrf
                    <input type="hidden" name="branch_id" id="branch_id">
                    
                    <div class="section-title"><i class="fas fa-building me-2" style="color: #f89884;"></i> Basic Vendor Information</div>
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="form-group">
                                <label>Company / Vendor Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required placeholder="Enter company name">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="form-group">
                                <label>Contact Person <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="contact_person" required placeholder="Enter contact person">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="phone" required placeholder="Enter phone number">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Enter email address">
                            </div>
                        </div>
                    </div>

                    <div class="section-title"><i class="fas fa-file-invoice me-2" style="color: #f89884;"></i> Tax & Drug License Details</div>
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-12">
                            <div class="form-group">
                                <label>GSTIN</label>
                                <input type="text" class="form-control" name="gst_number" placeholder="Enter GST number">
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-12">
                            <div class="form-group">
                                <label>Drug License No (DL No)</label>
                                <input type="text" class="form-control" name="drug_license_no" placeholder="Enter DL number">
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-12">
                            <div class="form-group">
                                <label>PAN No</label>
                                <input type="text" class="form-control" name="pan_no" placeholder="Enter PAN number">
                            </div>
                        </div>
                    </div>

                    <div class="section-title"><i class="fas fa-university me-2" style="color: #f89884;"></i> Bank & Ledger Accounts</div>
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="form-group">
                                <label>Bank Name</label>
                                <input type="text" class="form-control" name="bank_name" placeholder="e.g. HDFC Bank">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="form-group">
                                <label>Account Number</label>
                                <input type="text" class="form-control" name="account_no" placeholder="Enter account number">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="form-group">
                                <label>IFSC Code</label>
                                <input type="text" class="form-control" name="ifsc_code" placeholder="Enter IFSC code">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="form-group">
                                <label>Opening Balance (₹)</label>
                                <input type="number" step="0.01" class="form-control" name="opening_balance" value="0.00">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label>Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="address" rows="3" required placeholder="Enter complete vendor address"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3 pt-3 border-top">
                        <div class="col-12">
                            <button type="submit" class="btn btn-submit-main me-2" id="submitSupplierBtn">Save Vendor</button>
                            <a href="{{ route('supplier.index') }}" class="btn btn-cancel-main">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        const branchId = localStorage.getItem('selectedBranchId');
        $('#branch_id').val(branchId);

        $('#createSupplierForm').on('submit', function(e) {
            e.preventDefault();
            $('#submitSupplierBtn').prop('disabled', true).text('Saving...');

            const formData = $(this).serialize();

            $.ajax({
                url: '{{ url("api/suppliers") }}',
                type: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                data: formData,
                success: function(response) {
                    if (response.status) {
                        alert(response.message || 'Vendor saved successfully!');
                        window.location.href = '{{ route("supplier.index") }}';
                    } else {
                        alert(response.message || 'Error saving vendor');
                        $('#submitSupplierBtn').prop('disabled', false).text('Save Vendor');
                    }
                },
                error: function(err) {
                    $('#submitSupplierBtn').prop('disabled', false).text('Save Vendor');
                    let msg = 'Error saving vendor';
                    if (err.responseJSON) {
                        if (err.responseJSON.errors) {
                            msg = Object.values(err.responseJSON.errors).flat().join('\n');
                        } else if (err.responseJSON.message) {
                            msg = err.responseJSON.message;
                        }
                    }
                    alert(msg);
                }
            });
        });
    });
</script>
@endsection
