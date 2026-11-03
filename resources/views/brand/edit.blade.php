@extends('layout.app')

@section('content')
<style>
    /* Site Theme: #f89884 and #cfece0 with 50% pill radius on fields and buttons */
    .brand-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        background: #ffffff;
    }
    .brand-card .card-body {
        padding: 32px;
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
            <h4 class="page-title mb-0" style="font-weight: 700; font-size: 21px; color: #1e293b;">Edit Brand</h4>
            <a href="{{ route('brand.index') }}" class="btn btn-back-main">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                <div class="card brand-card">
                    <div class="card-body">
                        <form id="brandEditForm">
                            <input type="hidden" id="brand_id" value="{{ $id }}">
                            <div class="form-group">
                                <label>Brand Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" required placeholder="Enter brand name">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Optional notes or details"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="d-flex gap-2 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-submit-main me-2" id="updateBrandBtn">Update Brand</button>
                                <a href="{{ route('brand.index') }}" class="btn btn-cancel-main">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('token');
        const id = $('#brand_id').val();

        // Load existing
        $.ajax({
            url: `{{ url('api/brands') }}/${id}`,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(res) {
                if (res.status && res.brand) {
                    $('#name').val(res.brand.name);
                    $('#description').val(res.brand.description);
                    $('#status').val(res.brand.status || 'active');
                }
            }
        });

        $('#brandEditForm').on('submit', function(e) {
            e.preventDefault();
            $('#updateBrandBtn').prop('disabled', true).text('Updating...');

            const payload = {
                name: $('#name').val(),
                description: $('#description').val(),
                status: $('#status').val()
            };

            $.ajax({
                url: `{{ url('api/brands') }}/${id}`,
                type: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                data: payload,
                success: function(res) {
                    if (res.status) {
                        alert('Brand updated successfully!');
                        window.location.href = '{{ route("brand.index") }}';
                    }
                },
                error: function(err) {
                    $('#updateBrandBtn').prop('disabled', false).text('Update Brand');
                    alert('Error updating brand: ' + (err.responseJSON ? err.responseJSON.message : 'Unknown error'));
                }
            });
        });
    });
</script>
@endsection
