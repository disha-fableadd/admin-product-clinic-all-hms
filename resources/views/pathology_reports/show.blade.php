@extends('layout.app')

<style>
    .icon-style1 {
        background-color: white;
        color: #9dc3b3;
        padding: 5px;
        font-size: 20px;
        border-radius: 50%;
    }

    .icon-style2 {
        color: white;
        padding: 5px;
        font-size: 20px;
        border-radius: 50%;
    }
    .card-footer {
        background-color: #87ceb0 !important;
        padding: 12px 20px !important;
        border-radius: 10px 10px 0 0 !important;
    }
    .hdr-btn-group {
        display: flex !important;
        gap: 8px !important;
    }

    @media screen and (max-width: 767px) {
        .page-title {
            font-size: 17px !important;
            margin-bottom: 0 !important;
            line-height: 1.3 !important;
        }
        .hdr-btn-group {
            margin-bottom: 0 !important;
            gap: 8px !important;
        }
        .hdr-btn-group .btn-hdr {
            flex-shrink: 0 !important;
        }
        .card-footer h3 {
            font-size: 15px !important;
            float: none !important;
            line-height: 1.4 !important;
            word-break: break-word !important;
            margin: 0 !important;
        }
        .userProfile {
            width: 140px !important;
            height: 140px !important;
            margin: 0 auto 15px auto !important;
            display: block !important;
        }
        .card-body {
            padding: 15px !important;
        }
        p strong {
            font-size: 13.5px !important;
        }
        span.patient-name, span.test-name, span.test-code, span.sample-type, span.normal-range, span.cost, span.report-format, span.sample-date, span.report-date, span.result {
            font-size: 13.5px !important;
            word-break: break-word !important;
        }
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="min-height: 100vh; height: auto; padding-bottom: 50px;">
            <div class="row align-items-center mb-2" style="padding-top:15px">
                <div class="col pr-1">
                    <h4 class="page-title mb-0"><i class="fa fa-file-medical"></i> Pathology Report Details</h4>
                </div>
                <div class="col-auto pl-0 d-flex justify-content-end align-items-center hdr-btn-group" style="gap: 8px;">
                    <a href="javascript:void(0)" class="btn btn-primary btn-rounded btn-hdr download-report"
                        data-id="{{ $pathology_report_id }}">
                        <i class="fa fa-download"></i> <span class="hdr-btn-text">PDF</span>
                    </a>
                    @if(app('hasPermission')(20, 'view'))
                        <a href="{{ route('pathology_reports.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    @endif
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer d-flex align-items-center justify-content-between flex-wrap">
                            <h3 class="text-dark m-0"><i class="fa fa-info-circle icon-style2 mr-1"></i> <span
                                    class="patient-name"></span>'s <span class="test-name"></span> Details</h3>
                        </div>


                        <div class="card-body mt-3">

                            <div class="row">
                                <!-- Left Column: Patient Profile & Basic Test Info -->
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-sm-5 col-12 text-center margin-img mb-3">
                                            <p class="text-dark">
                                                <img id="file" src="" class="userProfile" width="180" height="180"
                                                    alt="Patient Image" style="border-radius:20px; object-fit: cover; box-shadow: 0 2px 8px rgba(0,0,0,0.08);"
                                                    onerror="this.onerror=null;this.src='{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}';">
                                            </p>
                                        </div>
                                        <div class="col-sm-7 col-12">
                                            <p><strong><i class="fa fa-user icon-style1"></i> Patient: </strong>
                                                <span class="patient-name"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p><strong><i class="fa fa-calendar-alt icon-style1"></i> Report Date: </strong> 
                                        <span class="report-date"></span>
                                    </p>
                                            <hr class="margin-text">
                                            <p><strong><i class="fa fa-barcode icon-style1"></i> Test Code: </strong> 
                                                <span class="test-code"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p><strong><i class="fa fa-vial icon-style1"></i> Sample Type: </strong> 
                                                <span class="sample-type"></span>
                                            </p>
                                          
                                             <hr class="margin-text">
                                    <p><strong><i class="fa fa-tag icon-style1"></i> Test Cost: </strong> 
                                        <span class="cost"></span>
                                    </p>
                                    <hr class="margin-text">
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column: Medical Specs, Result, Dates & Report File -->
                                <div class="col-lg-6">
                                    <p><strong><i class="fa fa-user-md icon-style1"></i> Test Name: </strong> 
                                                <span class="test-name"></span>
                                            </p>
                                    
                                    <hr class="margin-text">
                                    <p><strong><i class="fa fa-calendar icon-style1"></i> Sample Collected Date: </strong> 
                                        <span class="sample-date"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p><strong><i class="fa fa-sliders-h icon-style1"></i> Normal Range: </strong> 
                                        <span class="normal-range"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p><strong><i class="fa fa-notes-medical icon-style1"></i> Result: </strong> 
                                        <span class="result"></span>
                                    </p>
                                   
                                    <hr class="margin-text">
                                    <p><strong><i class="fa fa-file-alt icon-style1"></i> Report Format: </strong> 
                                        <span class="report-format"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <div class="margin-text">
                                        <p class="mb-2"><strong><i class="fa fa-file-pdf icon-style1"></i> Report File: </strong></p>
                                        <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                                            <a class="btn btn-outline-primary btn-sm" id="viewReportBtn" href="#" target="_blank" style="display:none; padding: 6px 14px; border-radius: 8px; font-weight: 500;">
                                                <i class="fas fa-file-alt mr-1"></i> View Uploaded
                                            </a>
                                            <span id="noFileText" class="text-muted" style="display:none; font-size: 13px;">No Uploaded File</span>
                                            <button type="button" class="btn btn-primary btn-sm download-report" data-id="{{ $pathology_report_id }}" style="background-color: #cfece0; color: #0c4b33; border: 1px solid #b8dfd0; padding: 6px 14px; border-radius: 8px; font-weight: 600;">
                                                <i class="fa fa-download mr-1"></i> Download PDF
                                            </button>
                                        </div>
                                    </div>
                                    <hr class="margin-text">
                                </div>
                            </div>

                            <div class="button mt-4 mb-4" style="display: flex; justify-content: end;">
                                @if(app('hasPermission')(20, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-report-btn"
                                        style="margin-right:10px; color:black">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif
                                @if(app('hasPermission')(20, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-report"
                                        data-id="{{ $pathology_report_id }}">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="successMessage" class="alert alert-success" style="display:none;"></div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var token = localStorage.getItem('token') || localStorage.getItem("authToken") || sessionStorage.getItem('token') || '';
            var reportId = "{{ $pathology_report_id }}";

            $.ajax({
                url: '/api/pathology-reports/' + reportId,
                type: 'GET',
                xhrFields: { withCredentials: true },
                headers: token ? { "Authorization": "Bearer " + token } : {},
                success: function (report) {
                    $('.report-id').text(report.id);
                    $("#file").attr("src", report.patient?.profile || '');
                    $('.patient-name').text(report.patient?.fullname ?? 'N/A');
                    $('.test-name').text(report.test?.test_name ?? 'N/A');
                    $('.test-code').text(report.test?.test_code ?? 'N/A');
                    $('.sample-type').text(report.test?.sample_type ?? 'N/A');
                    $('.normal-range').text(report.test?.normal_range ?? 'N/A');
                    $('.cost').text(report.test?.cost ? ('₹' + parseFloat(report.test.cost).toFixed(2)) : 'N/A');
                    $('.report-format').text(report.test?.report_format ?? 'N/A');
                    $('.sample-date').text(report.sample_collected_date ?? 'N/A');
                    $('.report-date').text(report.report_date ?? 'N/A');
                    $('.result').text(report.result ?? 'N/A');

                    if (report.report_file) {
                        let clean = report.report_file.replace(/^\/+/, '');
                        $('#viewReportBtn').attr('href', '/' + clean).show();
                        $('#noFileText').hide();
                    } else {
                        $('#viewReportBtn').hide();
                        $('#noFileText').show();
                    }

                    $('.edit-report-btn').attr('href', '/pathology_reports/edit/' + report.id);
                },
                error: function () {
                    alert('Failed to fetch report details.');
                }
            });

            $(document).on('click', '.download-report', function (e) {
                e.preventDefault();
                var id = $(this).data('id') || reportId;

                Swal.fire({
                    title: 'Generating PDF...',
                    text: 'Please wait while your Pathology Report is prepared.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '/api/pathology-reports/' + id + '/download',
                    method: 'GET',
                    xhrFields: {
                        responseType: 'blob',
                        withCredentials: true
                    },
                    headers: token ? { "Authorization": "Bearer " + token } : {},
                    success: function (response, status, xhr) {
                        Swal.close();
                        let filename = `PathologyReport_${id}.pdf`;
                        const disposition = xhr.getResponseHeader('Content-Disposition');
                        if (disposition && disposition.indexOf('filename=') !== -1) {
                            filename = disposition.split('filename=')[1].replace(/"/g, '');
                        }
                        const blob = new Blob([response], { type: 'application/pdf' });
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = filename;
                        link.click();
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to generate & download Pathology Report PDF.'
                        });
                    }
                });
            });

            $(document).on('click', '.delete-report', function () {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to undo this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/pathology-reports/' + id,
                            type: 'DELETE',
                            xhrFields: { withCredentials: true },
                            headers: token ? { "Authorization": "Bearer " + token } : {},
                            success: function (response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Report deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = "{{ route('pathology-reports.index') }}";
                                });
                            },
                            error: function () {
                                Swal.fire('Error', 'Failed to delete the report.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection