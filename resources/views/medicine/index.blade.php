@extends('layout.app')
<style>
    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    /* Cancel button text white (for visibility on red background) */
    .swal-cancel-btn {
        color: white !important;
    }

    colgroup {
        display: none;
    }

    .details-row {
        background-color: #f8f9fa;
    }
    .userProfile {
     width: 50px;
    height: 50px;
    object-fit: cover; /* best for table */
    border-radius: 50%;
}

    .details-content {
        padding: 10px;
        border-left: 3px solid #f89884;
        margin-left: 10px;
    }
      .card-header{
        background-color:#f89884 !important; 
    }
    .btn-rounded{
        background-color: #fed9cf !important;
    }

    button.expand-btn {
        background: #f89884;
        color: white;
        padding: 6px 8px;
        border-radius: 8px;
        border: none;
    }

    @media screen and (max-width: 767px) {
        .page-title {
            font-size: 19px !important;
            padding-left: 10px !important;
            text-align: left !important;
        }

        .report-icon {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding-top: 15px !important;
        }

        .report-icon .download {
            margin-left: 5px !important;
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

    /* Hide desktop columns on mobile */
    @media (max-width: 767px) {
        .d-none.d-md-table-cell {
            display: none !important;
        }
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px"></div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fas fa-pills px-2" style="font-size:20px"></i>Medicines
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(4, 'create'))
                                <a href="{{ route('medicine.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span></a>
                            @endif
                        </div>
                        <div class="card-body ">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="medicinetbl" class="table custom-table">
                                    <thead style="background-color:#ff8e29;">
                                        <tr>
                                            <th>Medicine</th>
                                            <th >Category</th>
                                            <th class="d-none d-md-table-cell">Batch No</th>
                                            <th class="d-none d-md-table-cell">Brand</th>
                                            <th class="d-none d-md-table-cell">Stock</th>
                                            <th class="d-none d-md-table-cell">MRP</th>
                                            <th class="d-none d-md-table-cell">Expiry</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="medicines"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= CDN Links ================= --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>

    <script>
        // Expand/collapse logic for mobile
        $(document).on('click', '.expand-btn', function() {
            let btn = $(this);
            let icon = btn.find('i');
            let tr = btn.closest('tr');

            if (!$.fn.DataTable.isDataTable('#medicinetbl')) {
                return;
            }

            let table = $('#medicinetbl').DataTable();
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
            let batchNo = data.batch_no ?? 'N/A';
            let quantity = data.quantity ?? 'N/A';
            let price = data.unit ?? 'N/A';
            let actions = `
                <div class="icon" style="cursor:pointer">
                    @if (app('hasPermission')(4, 'view'))
                        <i class="fa fa-eye m-r-5 icon3 view-medicine" data-id="${data.id}" title="View"></i>
                    @endif
                    @if (app('hasPermission')(4, 'update'))
                        <i class="fa fa-pencil m-r-5 icon1 edit-medicine" data-id="${data.id}" title="Edit"></i>
                    @endif
                    @if (app('hasPermission')(4, 'delete'))
                        <i class="fa fa-trash-o m-r-5 icon2 delete-medicine" data-id="${data.id}" title="Delete"></i>
                    @endif
                </div>
            `;

            let detailsRow = $(`
                <tr class="details-row">
                    <td colspan="6">
                        <div class="details-content">
                            <div><strong>Category:</strong> ${category}</div>
                            <div><strong>Batch No:</strong> ${batchNo}</div>
                            <div><strong>Quantity:</strong> ${quantity}</div>
                            <div><strong>Price:</strong> ${price}</div>
                            <div class="mt-2"><strong>Actions:</strong> ${actions}</div>
                        </div>
                    </td>
                </tr>
            `);

            tr.after(detailsRow);
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        });



        $(document).on('click', '#exportButton', function() {
            let branchId = localStorage.getItem('selectedBranchId');
            if (!branchId) {
                alert("Branch ID not found in localStorage!");
                return;
            }
            window.location.href = "{{ route('medicine.export') }}" + "?branch_id=" + branchId;
        });

        // Sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggle_btn');
            const sidebar = document.querySelector('.sidebar');

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    if (sidebar) {
                        sidebar.classList.toggle('mini-sidebar');
                    }
                });
            }
        });

        // Event logging (optional)
        function eventFired(type) {
            let n = document.querySelector('#demo_info');
            n.scrollTop = n.scrollHeight;
        }

        // Load medicines
        $(document).ready(function() {
            let branchId = localStorage.getItem('selectedBranchId');
            let token = localStorage.getItem('token');
            let medicineTable;

            function ucfirst(str) {
                if (!str) return 'N/A';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            function initMedicineTable() {
                if ($.fn.DataTable.isDataTable('#medicinetbl')) {
                    try {
                        let existingTable = $('#medicinetbl').DataTable();
                        existingTable.destroy();
                        $('#medicinetbl').removeClass('dataTable');
                        $('#medicinetbl tbody').empty();
                        $.removeData($('#medicinetbl')[0], 'DataTable');
                        $.removeData($('#medicinetbl')[0], 'DataTables_DataTable');
                        $('#medicinetbl').off();
                    } catch (e) {
                        $('#medicinetbl tbody').empty();
                        $.removeData($('#medicinetbl')[0]);
                    }
                }

                if ($('#medicinetbl tbody').length === 0) {
                    $('#medicinetbl').append('<tbody id="medicines"></tbody>');
                }

                setTimeout(function () {
                    medicineTable = $('#medicinetbl').DataTable({
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
                                url: '{{ url('/api/medicines') }}',
                                type: 'GET',
                                dataType: 'json',
                                data: {
                                    branch_id: branchId,
                                    page: page,
                                    per_page: perPage,
                                    draw: data.draw,
                                    start: data.start,
                                    length: data.length,
                                    search: data.search?.value || ''
                                },
                                headers: {
                                    "Authorization": "Bearer " + (token || localStorage.getItem('token'))
                                },
                                success: function (response) {
                                    const list = response.data || response.medicines || (Array.isArray(response) ? response : []);
                                    const total = response.recordsTotal ?? response.pagination?.total ?? list.length;
                                    const filtered = response.recordsFiltered ?? response.pagination?.total ?? list.length;
                                    callback({
                                        draw: data.draw || response.draw || 1,
                                        recordsTotal: total,
                                        recordsFiltered: filtered,
                                        data: list
                                    });
                                },
                                error: function (xhr, status, error) {
                                    console.error("Error fetching data: " + error);
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
                                data: null,
                                render: function (data, type, row) {
                                    const defaultImg = '{{ asset(env('IMAGE_PATH') . 'admin/assets/img/default.webp') }}';
                                    const imgSrc = (row.image && row.image.trim() !== '') ? row.image : defaultImg;
                                    return `
                                        <div class="view-medicine d-flex align-items-center" data-id="${row.id}" style="cursor:pointer">
                                            <img src="${imgSrc}"
                                                onerror="this.onerror=null; this.src='${defaultImg}';"
                                                class="rounded-circle userProfile me-2"
                                                alt="Medicine Image">
                                            <span><h2 style="font-size:14px; margin:0;">${ucfirst(row.name)}</h2></span>
                                        </div>
                                    `;
                                }
                            },
                            {
                                data: "category_name",
                                render: function (data, type, row) {
                                    return `<span class="view-medicine" data-id="${row.id}" style="cursor:pointer">${ucfirst(data)}</span>`;
                                }
                            },
                            {
                                data: "batch_no",
                                render: function (data, type, row) {
                                    return `<span class="view-medicine d-none d-md-table-cell" data-id="${row.id}">${data || 'N/A'}</span>`;
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "brand_name",
                                render: function (data, type, row) {
                                    return `<span class="view-medicine d-none d-md-table-cell" data-id="${row.id}">${data || 'N/A'}</span>`;
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "quantity",
                                render: function (data, type, row) {
                                    let qty = (data !== null && data !== undefined) ? parseInt(data) : 0;
                                    let lowStockThreshold = (row.low_stock_qty !== null && row.low_stock_qty !== undefined && row.low_stock_qty !== '') ? parseInt(row.low_stock_qty) : 10;
                                    let badge = (qty <= lowStockThreshold) ? '<span class="badge ms-1" style="background-color: #ea5455; color: #fff; border-radius: 4px; font-size: 11px; padding: 3px 8px; font-weight: 500;">Low Stock</span>' : '';
                                    return `<span class="d-none d-md-table-cell"><span style="border: 1px solid #ececec; padding: 3px 8px; border-radius: 4px; display: inline-block; font-weight: 600;">${qty}</span> ${badge}</span>`;
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "unit",
                                render: function (data, type, row) {
                                    let val = row.mrp || data || 0;
                                    return `<span class="d-none d-md-table-cell">₹${parseFloat(val).toFixed(2)}</span>`;
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "expiry_date",
                                render: function (data, type, row) {
                                    if (!data || data === 'N/A') {
                                        return `<span class="d-none d-md-table-cell text-muted">N/A</span>`;
                                    }
                                    let dateObj = new Date(data);
                                    if (isNaN(dateObj.getTime())) {
                                        return `<span class="d-none d-md-table-cell">${data}</span>`;
                                    }
                                    let m = String(dateObj.getMonth() + 1).padStart(2, '0');
                                    let y = dateObj.getFullYear();
                                    let formattedDate = `${m}/${y}`;

                                    let today = new Date();
                                    today.setHours(0, 0, 0, 0);
                                    let expiryThreshold = new Date(today);
                                    expiryThreshold.setDate(today.getDate() + 60);

                                    if (dateObj < today) {
                                        return `<span class="d-none d-md-table-cell"><span style="color: #ea5455; font-weight: 600;">${formattedDate}</span> <span class="badge ms-1" style="background-color: #dc2626; color: #fff; border-radius: 4px; font-size: 10px; padding: 2px 6px; font-weight: 500;">Expired</span></span>`;
                                    } else if (dateObj >= today && dateObj <= expiryThreshold) {
                                        return `<span class="d-none d-md-table-cell"><span style="color: #ea5455; font-weight: 600;">${formattedDate}</span> <span class="badge ms-1" style="background-color: #ea5455; color: #fff; border-radius: 4px; font-size: 10px; padding: 2px 6px; font-weight: 500;">Expiring</span></span>`;
                                    }
                                    return `<span class="d-none d-md-table-cell">${formattedDate}</span>`;
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "id",
                                render: function (data, type, row) {
                                    return `
                                        <div class="icon" style="cursor:pointer">
                                            @if (app('hasPermission')(4, 'view'))
                                                <i class="fa fa-eye m-r-5 icon3 view-medicine" data-id="${row.id}" title="View"></i>
                                            @endif
                                            @if (app('hasPermission')(4, 'update'))
                                                <i class="fa fa-pencil m-r-5 icon1 edit-medicine" data-id="${row.id}" title="Edit"></i>
                                            @endif
                                            @if (app('hasPermission')(4, 'delete'))
                                                <i class="fa fa-trash-o m-r-5 icon2 delete-medicine" data-id="${row.id}" title="Delete"></i>
                                            @endif
                                        </div>
                                    `;
                                },
                                className: "d-none d-md-table-cell",
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
                        ],
                        drawCallback: function () {
                            // Status indicators (Low Stock / Expiring) are cleanly displayed directly in table rows
                        }
                    })
                    .on('order.dt', () => eventFired('Order'))
                    .on('search.dt', () => eventFired('Search'))
                    .on('page.dt', () => eventFired('Page'));
                }, 50);
            }

            initMedicineTable();
        });

        // ===================== CRUD Actions =====================
        $(document).on('click', '.view-medicine', function() {
            var medicineId = $(this).data('id');
            window.location.href = '/medicine/show/' + medicineId;
        });

        $(document).on('click', '.edit-medicine', function() {
            var medicineId = $(this).data('id');
            window.location.href = '/medicine/edit/' + medicineId;
        });

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
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'swal-confirm-btn',
                    cancelButton: 'swal-cancel-btn'
                }
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
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            if (xhr.status === 409) {
                                Swal.fire('Cannot Delete', xhr.responseJSON.message, 'warning');
                            } else {
                                console.log(xhr.responseText);
                                Swal.fire('Error',
                                    'Please remove medicine from patient medicine records first.',
                                    'error');
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
