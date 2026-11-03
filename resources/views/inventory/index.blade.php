@extends('layout.app')
<style>
    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    .swal-cancel-btn {
        color: white !important;
    }

    .details-row {
        background-color: #f8f9fa;
    }

    colgroup {
        display: none;
    }

    .details-content {
        padding: 10px;
        border-left: 3px solid #f89884;
        margin-left: 10px;
    }

    button.expand-btn {
        background: #f89884 !important;
        color: white !important;
        border: none !important;
        padding: 8px !important;
        border-radius: 10px !important;
    }

    .card-header{
        background-color:#f89884 !important; 
    }
    #inventorytbl th:last-child,
    #inventorytbl td:last-child,
    #inventorytbl th:nth-child(7),
    #inventorytbl td:nth-child(7) {
        white-space: nowrap !important;
        width: 1% !important;
    }
    .btn-rounded{
        background-color: #fed9cf !important;
    }


    @media screen and (max-width:767px) {
        .page-title {
            font-size: 20px !important;
            padding-left: 7px !important;
        }

        .d-none.d-md-table-cell {
            display: none !important;
        }
    }

    @media (min-width: 768px) {
        .d-table-cell.d-md-none {
            display: none !important;
        }

        .details-row {
            display: none !important;
        }
    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px"></div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-cogs px-2" style="font-size:20px"></i>All Inventory
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportInventoryBtn">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span> 
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="inventorytbl" class="table custom-table">
                                    <thead style="background-color:#ff8e29;" class="text-center">
                                        <tr>
                                            <th>Medicine</th>
                                            <th class="d-none d-md-table-cell">Category</th>
                                            <th class="d-none d-md-table-cell">Batch Number</th>
                                            <th class="d-none d-md-table-cell">Quantity</th>
                                            <th class="d-none d-md-table-cell">Price</th>
                                            <th class="d-none d-md-table-cell">Expiry Date</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="inventoryBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Quantity Modal -->
        <div class="modal fade" id="updateQuantityModal" tabindex="-1" role="dialog" aria-labelledby="updateQuantityModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #f89884; color: white;">
                        <h5 class="modal-title" id="updateQuantityModalLabel">Update Medicine Quantity</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background:none; border:none; font-size:26px; line-height:1; cursor:pointer;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="updateQuantityForm">
                        <div class="modal-body">
                            <input type="hidden" id="updateMedicineId">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medicineNameDisplay">Medicine</label>
                                        <input type="text" class="form-control" id="medicineNameDisplay" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medicineCategoryDisplay">Category</label>
                                        <input type="text" class="form-control" id="medicineCategoryDisplay" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medicineBatchDisplay">Batch Number</label>
                                        <input type="text" class="form-control" id="medicineBatchDisplay" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medicineExpiryDisplay">Expiry Date</label>
                                        <input type="text" class="form-control" id="medicineExpiryDisplay" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="currentQuantityDisplay">Current Quantity</label>
                                        <input type="text" class="form-control" id="currentQuantityDisplay" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medicinePriceDisplay">Price</label>
                                        <input type="text" class="form-control" id="medicinePriceDisplay" readonly>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Stock Update</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="stockAction" id="addStock" value="add" checked>
                                    <label class="form-check-label" for="addStock">Add Quantity</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="stockAction" id="minusStock" value="minus">
                                    <label class="form-check-label" for="minusStock">Minus Quantity</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label id="adjustmentLabel">How much to Add?</label>
                                <input type="number" class="form-control" id="adjustmentQuantity" min="1" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 50px;">Cancel</button>
                            <button type="submit" class="btn btn-primary" style="border-radius: 50px;">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Stock & Sales History Modal -->
        <div class="modal fade" id="stockHistoryModal" tabindex="-1" role="dialog" aria-labelledby="stockHistoryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content" style="border-radius: 18px; border: none; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                    <div class="modal-header" style="background-color: #f89884; color: white; padding: 14px 22px;">
                        <h5 class="modal-title fw-bold text-white d-flex align-items-center" id="stockHistoryModalLabel">
                            <i class="fa fa-history me-2"></i> Stock & Sales History &mdash; <span id="histMedicineName" class="ms-1 fw-bold"></span>
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background:none; border:none; font-size:26px; line-height:1; cursor:pointer;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4" style="background-color: #fafbfc;">
                        <!-- KPI Stat Cards -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-3 col-6">
                                <div class="card p-3 border-0 shadow-sm" style="border-radius: 12px; background: #fff5f2;">
                                    <span class="text-muted" style="font-size: 11.5px; font-weight: 600; text-transform: uppercase;">Current Stock</span>
                                    <h3 class="mb-0 fw-bold mt-1" id="histCurrentStock" style="color: #f89884;">0</h3>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="card p-3 border-0 shadow-sm" style="border-radius: 12px; background: #f0fdf4;">
                                    <span class="text-muted" style="font-size: 11.5px; font-weight: 600; text-transform: uppercase;">Total Units Sold</span>
                                    <h3 class="mb-0 fw-bold mt-1 text-success" id="histTotalSold">0</h3>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="card p-3 border-0 shadow-sm" style="border-radius: 12px; background: #f0f9ff;">
                                    <span class="text-muted" style="font-size: 11.5px; font-weight: 600; text-transform: uppercase;">Total Purchased</span>
                                    <h3 class="mb-0 fw-bold mt-1" id="histTotalPurchased" style="color: #0284c7;">0</h3>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="card p-3 border-0 shadow-sm" style="border-radius: 12px; background: #faf5ff;">
                                    <span class="text-muted" style="font-size: 11.5px; font-weight: 600; text-transform: uppercase;">Selling Price (MRP)</span>
                                    <h3 class="mb-0 fw-bold mt-1" id="histPrice" style="color: #9333ea;">₹0.00</h3>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs -->
                        <ul class="nav nav-pills mb-3" id="modalHistoryTabs" role="tablist" style="gap: 8px;">
                            <li class="nav-item">
                                <button class="nav-link active modal-tab-btn" id="modal-patient-tab" type="button" style="border-radius: 50px; font-weight: 600; font-size: 12.5px; padding: 7px 18px; background-color: #f89884; color: #fff;">
                                    <i class="fa fa-shopping-cart me-1"></i> Patient Sales & Dispensing (<span id="modalPatientCount">0</span>)
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link modal-tab-btn" id="modal-vendor-tab" type="button" style="border-radius: 50px; font-weight: 600; font-size: 12.5px; padding: 7px 18px; background-color: #f1f5f9; color: #475569;">
                                    <i class="fa fa-truck me-1"></i> Vendor Purchases / Stock In (<span id="modalVendorCount">0</span>)
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content bg-white p-3 border" style="border-radius: 14px;">
                            <!-- Patient Sales Pane -->
                            <div class="tab-pane fade show active" id="modalPatientPane">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0" id="modalPatientTable" style="width: 100%; font-size: 12px;">
                                        <thead style="background-color: #cfece0; color: #1e293b;">
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Ref / Voucher</th>
                                                <th>Patient Name</th>
                                                <th>Phone</th>
                                                <th>Batch</th>
                                                <th>Qty Sold</th>
                                                <th>Price (₹)</th>
                                                <th>Total (₹)</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="modalPatientTableBody">
                                            <tr><td colspan="10" class="text-center py-4 text-muted">No sales history found</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Vendor Purchases Pane -->
                            <div class="tab-pane fade" id="modalVendorPane" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0" id="modalVendorTable" style="width: 100%; font-size: 12px;">
                                        <thead style="background-color: #cfece0; color: #1e293b;">
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Bill No</th>
                                                <th>Vendor / Supplier</th>
                                                <th>Batch</th>
                                                <th>Qty In</th>
                                                <th>Free Qty</th>
                                                <th>Total Qty</th>
                                                <th>Rate (₹)</th>
                                                <th>Total (₹)</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="modalVendorTableBody">
                                            <tr><td colspan="11" class="text-center py-4 text-muted">No purchase records found</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="padding: 10px 20px; background-color: #f8f9fa;">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 50px; font-weight: 600; padding: 6px 22px;">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script>
        $(document).on('click', '#exportInventoryBtn', function() {
            let branchId = localStorage.getItem('selectedBranchId');
            if (!branchId) {
                alert("Branch ID not found in localStorage!");
                return;
            }
            window.location.href = "{{ route('inventory.export') }}" + "?branch_id=" + branchId;
        });

        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggle_btn');
            const sidebar = document.querySelector('.sidebar');
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('mini-sidebar');
                });
            }
        });

        $(document).ready(function() {
            let branchId = localStorage.getItem('selectedBranchId');
            let token = localStorage.getItem('token') || sessionStorage.getItem('token');
            let userId = sessionStorage.getItem('user_id');
            let inventoryTable;

            function formatDate(value) {
                if (!value) return 'N/A';
                let d = new Date(value);
                return isNaN(d.getTime()) ? 'N/A' : d.toLocaleDateString();
            }

            function initInventoryTable() {
                if ($.fn.DataTable.isDataTable("#inventorytbl")) {
                    try {
                        let existingTable = $('#inventorytbl').DataTable();
                        existingTable.destroy();
                        $('#inventorytbl').removeClass('dataTable');
                        $('#inventorytbl tbody').empty();
                        $.removeData($('#inventorytbl')[0], 'DataTable');
                        $.removeData($('#inventorytbl')[0], 'DataTables_DataTable');
                        $('#inventorytbl').off();
                    } catch (e) {
                        $('#inventorytbl tbody').empty();
                        $.removeData($('#inventorytbl')[0]);
                    }
                }

                if ($('#inventorytbl tbody').length === 0) {
                    $('#inventorytbl').append('<tbody id="inventoryBody"></tbody>');
                }

                setTimeout(function () {
                    inventoryTable = $('#inventorytbl').DataTable({
                        processing: true,
                        serverSide: true,
                        retrieve: true,
                        destroy: true,
                        paging: true,
                        searching: true,
                        ordering: true,
                        ajax: function (data, callback) {
                            const page = Math.floor(data.start / data.length) + 1;
                            const perPage = data.length;
                            $.ajax({
                                url: "{{ url('/api/medicines') }}",
                                type: "GET",
                                dataType: "json",
                                data: {
                                    branch_id: branchId,
                                    page: page,
                                    per_page: perPage,
                                    search: data.search?.value || ''
                                },
                                headers: {
                                    "Authorization": "Bearer " + token
                                },
                                success: function (response) {
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: response.pagination?.total || response.recordsTotal || 0,
                                        recordsFiltered: response.pagination?.total || response.recordsFiltered || 0,
                                        data: response.data || []
                                    });
                                },
                                error: function (xhr) {
                                    console.log("Fetch error:", xhr.responseText);
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: 0,
                                        recordsFiltered: 0,
                                        data: []
                                    });
                                }
                            });
                        },
                        columns: [
                            {
                                data: "name",
                                render: function (data) { return data ?? 'N/A'; }
                            },
                            {
                                data: "category_name",
                                render: function (data) { return data ?? 'N/A'; },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "batch_no",
                                render: function (data) { return data ?? 'N/A'; },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "quantity",
                                render: function (data) { return data ?? '0'; },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "unit",
                                render: function (data) { return data ?? '0'; },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "expiry_date",
                                render: function (data) { return formatDate(data); },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: null,
                                render: function (data, type, row) {
                                    return `
                                        <div class="d-inline-flex align-items-center flex-nowrap" style="white-space: nowrap; gap: 8px;">
                                            <button class="btn btn-sm view-stock-history-btn" 
                                                data-id="${row.id}" 
                                                data-name="${row.name}"
                                                style="background-color: #cfece0; color: #1e293b; border-radius: 50px; font-weight: 600; padding: 5px 14px; border: none; white-space: nowrap;">
                                                <i class="fa fa-history text-success me-1"></i> History
                                            </button>
                                            <button class="btn btn-primary btn-sm update-quantity-btn" 
                                                data-id="${row.id}" 
                                                data-name="${row.name}" 
                                                data-category="${row.category_name ?? 'N/A'}"
                                                data-batch="${row.batch_no ?? 'N/A'}"
                                                data-quantity="${row.quantity ?? '0'}"
                                                data-price="${row.unit ?? '0'}"
                                                data-expiry="${row.expiry_date ?? 'N/A'}"
                                                style="border-radius: 50px; font-weight: 600; padding: 5px 14px; white-space: nowrap;">
                                                <i class="fa fa-plus"></i> Add/edit Stock
                                            </button>
                                        </div>
                                    `;
                                },
                                className: "d-none d-md-table-cell text-nowrap",
                                orderable: false
                            },
                            {
                                data: null,
                                render: function () {
                                    return `
                                        <button class="expand-btn">
                                            <i class="fa fa-chevron-down"></i>
                                        </button>
                                    `;
                                },
                                className: "d-table-cell d-md-none text-center",
                                orderable: false
                            }
                        ]
                    });
                }, 50);
            }

            initInventoryTable();

            // Open Update Quantity Modal
            $(document).on('click', '.update-quantity-btn', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let category = $(this).data('category');
                let batch = $(this).data('batch');
                let quantity = $(this).data('quantity');
                let price = $(this).data('price');
                let expiry = $(this).data('expiry');

                $('#updateMedicineId').val(id);
                $('#medicineNameDisplay').val(name);
                $('#medicineCategoryDisplay').val(category);
                $('#medicineBatchDisplay').val(batch);
                $('#currentQuantityDisplay').val(quantity);
                $('#medicinePriceDisplay').val(price);
                $('#medicineExpiryDisplay').val(expiry !== 'N/A' ? new Date(expiry).toLocaleDateString() : 'N/A');
                
                $('#adjustmentQuantity').val('');
                $('#addStock').prop('checked', true);
                $('#adjustmentLabel').text('How much to Add?');
                
                $('#updateQuantityModal').modal('show');
            });

            // Expand Button (Mobile)
            $(document).on('click', '.expand-btn', function() {
                let btn = $(this);
                let icon = btn.find('i');
                let tr = btn.closest('tr');

                if (!$.fn.DataTable.isDataTable('#inventorytbl')) {
                    return;
                }

                let table = $('#inventorytbl').DataTable();
                let row = table.row(tr);
                let data = row.data();
                if (!data) return;

                let nextRow = tr.next('.details-row');
                if (nextRow.length) {
                    nextRow.slideToggle(300);
                    icon.toggleClass('fa-chevron-down fa-chevron-up');
                    return;
                }

                let category = data.category_name ?? 'N/A';
                let batch = data.batch_no ?? 'N/A';
                let quantity = data.quantity ?? '0';
                let price = data.unit ?? '0';
                let expiry = formatDate(data.expiry_date);
                let actionBtn = `
                    <div class="d-flex align-items-center flex-nowrap" style="white-space: nowrap; gap: 8px;">
                        <button class="btn btn-sm view-stock-history-btn" 
                            data-id="${data.id}" 
                            data-name="${data.name}"
                            style="background-color: #cfece0; color: #1e293b; border-radius: 50px; font-weight: 600; padding: 5px 14px; border: none; white-space: nowrap;">
                            <i class="fa fa-history text-success me-1"></i> History
                        </button>
                        <button class="btn btn-primary btn-sm update-quantity-btn" 
                            data-id="${data.id}" 
                            data-name="${data.name}" 
                            data-category="${data.category_name ?? 'N/A'}"
                            data-batch="${data.batch_no ?? 'N/A'}"
                            data-quantity="${data.quantity ?? '0'}"
                            data-price="${data.unit ?? '0'}"
                            data-expiry="${data.expiry_date ?? 'N/A'}"
                            style="border-radius: 50px; font-weight: 600; padding: 5px 14px; white-space: nowrap;">
                            <i class="fa fa-plus"></i> Add/edit Stock
                        </button>
                    </div>
                `;

                let detailsRow = `
                    <tr class="details-row">
                        <td colspan="8">
                            <div class="details-content">
                                <div><strong>Category:</strong> ${category}</div>
                                <div><strong>Batch Number:</strong> ${batch}</div>
                                <div><strong>Quantity:</strong> ${quantity}</div>
                                <div><strong>Price:</strong> ${price}</div>
                                <div><strong>Expiry Date:</strong> ${expiry}</div>
                                <div class="mt-2"><strong>Action:</strong><br>${actionBtn}</div>
                            </div>
                        </td>
                    </tr>
                `;

                tr.after(detailsRow);
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            });

            $(window).on('resize', function() {
                if ($(window).width() >= 768) {
                    $('.details-row').remove();
                    $('.expand-btn i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
                }
            });

            // Handle Stock Action Radio Button Change
            $(document).on('change', 'input[name="stockAction"]', function() {
                if ($(this).val() === 'add') {
                    $('#adjustmentLabel').text('How much to Add?');
                } else {
                    $('#adjustmentLabel').text('How much to Minus?');
                }
            });

            // Handle Quantity Update Form Submission
            $('#updateQuantityForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#updateMedicineId').val();
                let currentQty = parseInt($('#currentQuantityDisplay').val());
                let adjustment = parseInt($('#adjustmentQuantity').val());
                let action = $('input[name="stockAction"]:checked').val();
                
                let newQuantity = action === 'add' ? currentQty + adjustment : currentQty - adjustment;
                
                if (newQuantity < 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Quantity',
                        text: 'Stock cannot be negative.'
                    });
                    return;
                }

                $.ajax({
                    url: `/api/medicines/${id}/update-quantity`,
                    type: "POST",
                    dataType: "json",
                    data: {
                        quantity: newQuantity
                    },
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#updateQuantityModal').modal('hide');
                            if (inventoryTable) {
                                inventoryTable.ajax.reload(null, false);
                            } else {
                                initInventoryTable();
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error("Update error:", xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update quantity.'
                        });
                    }
                });
            });
            // Tab switcher in Stock History Modal
            $(document).on('click', '#modal-patient-tab', function() {
                $(this).css({'background-color': '#f89884', 'color': '#ffffff'});
                $('#modal-vendor-tab').css({'background-color': '#f1f5f9', 'color': '#475569'});
                $('#modalPatientPane').addClass('show active').show();
                $('#modalVendorPane').removeClass('show active').hide();
            });

            $(document).on('click', '#modal-vendor-tab', function() {
                $(this).css({'background-color': '#f89884', 'color': '#ffffff'});
                $('#modal-patient-tab').css({'background-color': '#f1f5f9', 'color': '#475569'});
                $('#modalVendorPane').addClass('show active').show();
                $('#modalPatientPane').removeClass('show active').hide();
            });

            // Click History Button
            $(document).on('click', '.view-stock-history-btn', function() {
                let medId = $(this).data('id');
                let medName = $(this).data('name') || 'Medicine';

                $('#histMedicineName').text(medName);
                $('#histCurrentStock').text('...');
                $('#histTotalSold').text('...');
                $('#histTotalPurchased').text('...');
                $('#histPrice').text('...');
                $('#modalPatientTableBody').html('<tr><td colspan="10" class="text-center py-4"><span class="spinner-border spinner-border-sm text-primary"></span> Loading history...</td></tr>');
                $('#modalVendorTableBody').html('<tr><td colspan="11" class="text-center py-4"><span class="spinner-border spinner-border-sm text-primary"></span> Loading purchases...</td></tr>');

                $('#modal-patient-tab').trigger('click');
                $('#stockHistoryModal').modal('show');

                $.ajax({
                    url: `/api/medicines/${medId}`,
                    type: 'GET',
                    headers: { 'Authorization': 'Bearer ' + token },
                    success: function(res) {
                        if (!res.status && !res.medicine) {
                            $('#modalPatientTableBody').html('<tr><td colspan="10" class="text-center py-3 text-muted">No history found</td></tr>');
                            return;
                        }

                        const med = res.medicine;
                        const summary = res.summary || {};

                        $('#histCurrentStock').text(summary.current_stock ?? med.quantity ?? 0);
                        $('#histTotalSold').text(summary.total_sold ?? 0);
                        $('#histTotalPurchased').text(summary.total_purchased ?? 0);
                        $('#histPrice').text('₹' + parseFloat(med.mrp || med.unit || 0).toFixed(2));

                        // Render Patient Sales
                        const history = res.history || [];
                        $('#modalPatientCount').text(history.length);
                        if (history.length > 0) {
                            let html = '';
                            history.forEach((row, idx) => {
                                let action = row.view_url 
                                    ? `<a href="${row.view_url}" target="_blank" class="btn btn-sm" style="background:#cfece0; color:#1e293b; border-radius:50px; font-size:11px; font-weight:600;"><i class="fa fa-file-invoice text-success me-1"></i>Invoice</a>`
                                    : '<span class="text-muted">-</span>';
                                html += `<tr>
                                    <td>${idx + 1}</td>
                                    <td>${row.date}</td>
                                    <td><strong>${row.ref_no}</strong></td>
                                    <td><strong>${row.patient_name}</strong></td>
                                    <td>${row.patient_phone}</td>
                                    <td>${row.batch_no}</td>
                                    <td><span class="badge bg-light text-dark fw-bold border">${row.quantity}</span></td>
                                    <td>₹${parseFloat(row.price).toFixed(2)}</td>
                                    <td class="text-success fw-bold">₹${parseFloat(row.total_amount).toFixed(2)}</td>
                                    <td>${action}</td>
                                </tr>`;
                            });
                            $('#modalPatientTableBody').html(html);
                        } else {
                            $('#modalPatientTableBody').html('<tr><td colspan="10" class="text-center py-4 text-muted">No patient sales or dispensing records found for this medicine.</td></tr>');
                        }

                        // Render Vendor Purchases
                        const purchases = res.purchases_history || [];
                        $('#modalVendorCount').text(purchases.length);
                        if (purchases.length > 0) {
                            let phtml = '';
                            purchases.forEach((p, idx) => {
                                let action = p.view_url 
                                    ? `<a href="${p.view_url}" target="_blank" class="btn btn-sm" style="background:#cfece0; color:#1e293b; border-radius:50px; font-size:11px; font-weight:600;"><i class="fa fa-file-alt text-success me-1"></i>View Bill</a>`
                                    : '<span class="text-muted">-</span>';
                                phtml += `<tr>
                                    <td>${idx + 1}</td>
                                    <td>${p.date}</td>
                                    <td><strong>${p.bill_no}</strong></td>
                                    <td><strong>${p.vendor_name}</strong></td>
                                    <td>${p.batch_no}</td>
                                    <td><span class="badge bg-light text-dark fw-bold border">${p.quantity}</span></td>
                                    <td>${p.free_quantity}</td>
                                    <td class="fw-bold">${p.total_quantity}</td>
                                    <td>₹${parseFloat(p.rate).toFixed(2)}</td>
                                    <td class="text-primary fw-bold">₹${parseFloat(p.total_amount).toFixed(2)}</td>
                                    <td>${action}</td>
                                </tr>`;
                            });
                            $('#modalVendorTableBody').html(phtml);
                        } else {
                            $('#modalVendorTableBody').html('<tr><td colspan="11" class="text-center py-4 text-muted">No vendor purchase records found for this medicine.</td></tr>');
                        }
                    },
                    error: function(err) {
                        $('#modalPatientTableBody').html('<tr><td colspan="10" class="text-center py-3 text-danger">Failed to load history data.</td></tr>');
                    }
                });
            });

            // Reliable modal close handler for both x and Cancel buttons
            $(document).on('click', '.modal .close, [data-dismiss="modal"], [data-bs-dismiss="modal"]', function() {
                $(this).closest('.modal').modal('hide');
            });
        });
    </script>
@endsection
