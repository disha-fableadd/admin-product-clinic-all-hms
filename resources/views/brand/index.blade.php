@extends('layout.app')

<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/category-index.css') }}">
@section('content')
    <div class="page-wrapper">
        <div class="content content-height">
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header card-header-bg">
                            <h2 class="card-title d-inline-block text-white">
                                <i class="fas fa-tag px-2" style="font-size:20px"></i> All Brands
                            </h2>
                            <a href="{{ route('brand.create') }}" class="btn btn-rounded float-right btn-bg-fed9cf btn-padding">
                                <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add Brand</span>
                            </a>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                               <table id="brandtbl" class="table custom-table mt-3 display">
                                   <thead class="thead-bg">
                                       <tr>
                                           <th>Brand Name</th>
                                           <th>Description</th>
                                           <th>Status</th>
                                           <th class="th-width">Action</th>
                                       </tr>
                                   </thead>
                                    <tbody id="brandTableBody"></tbody>
                                </table>
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

    <script>
        $(document).ready(function() {
            const token = localStorage.getItem('token');
            const branchId = localStorage.getItem('selectedBranchId');

            function fetchBrands() {
                $.ajax({
                    url: '{{ url("api/brands") }}',
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    data: { branch_id: branchId },
                    success: function(response) {
                        if (response.status && response.brands) {
                            let rows = '';
                            response.brands.forEach(brand => {
                                rows += `
                                    <tr>
                                        <td><strong>${brand.name}</strong></td>
                                        <td>${brand.description || 'N/A'}</td>
                                        <td><span class="badge ${brand.status === 'active' ? 'bg-success' : 'bg-danger'} text-white">${brand.status || 'active'}</span></td>
                                        <td>
                                            <a href="{{ url('brand/edit') }}/${brand.id}" class="btn btn-sm btn-outline-primary me-1"><i class="fa fa-edit"></i></a>
                                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${brand.id}"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                `;
                            });
                            $('#brandTableBody').html(rows);
                            if ($.fn.DataTable.isDataTable('#brandtbl')) {
                                $('#brandtbl').DataTable().destroy();
                            }
                            $('#brandtbl').DataTable({ pageLength: 10 });
                        }
                    }
                });
            }

            fetchBrands();

            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this brand?')) {
                    $.ajax({
                        url: `{{ url('api/brands') }}/${id}`,
                        type: 'DELETE',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        },
                        success: function(res) {
                            alert(res.message || 'Brand deleted');
                            fetchBrands();
                        }
                    });
                }
            });
        });
    </script>
@endsection

