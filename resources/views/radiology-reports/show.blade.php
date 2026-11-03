@extends('layout.app')
<style>
    .container {
        width: 60%;
        margin: auto;
    }

    .header1 {
        background: #cfece0;
        color: black;
        padding: 10px 15px;
        font-size: 18px;
        font-weight: bold;
        margin-top: 50px;
        border-radius: 15px;
    }

    .card-body {
        height: auto;
        padding: 20px !important;
    }

    .card {
        margin: 0;
    }

    .info-label {
        font-weight: bold;
        color: #333;
    }

    .info-value {
        color: #555;
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
        p strong, .info-value {
            font-size: 13.5px !important;
            word-break: break-word !important;
        }
    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content" style="min-height: 100vh; height: auto; padding-bottom: 50px;">
            <div class="row mt-3 align-items-center mb-2">
                <div class="col pr-1">
                    <h4 class="page-title mb-0" style="text-align:left;">
                        Radiology Report Details
                    </h4>
                </div>
                <div class="col-auto pl-0 d-flex justify-content-end align-items-center hdr-btn-group" style="gap: 8px;">
                    <a href="javascript:void(0)" class="btn btn-primary btn-rounded btn-hdr download-report" data-id="{{ $radiology_report_id ?? '' }}" style="background-color: #fed9cf !important; color: #000;">
                        <i class="fa fa-download"></i> <span class="hdr-btn-text">PDF</span>
                    </a>
                    @if(app('hasPermission')(22, 'view'))
                        <a href="{{ route('radiology-reports.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    @endif
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer d-flex align-items-center justify-content-between flex-wrap">
                            <h3 class="text-dark m-0">
                                <i class="fa fa-info-circle icon-style2 mr-1"></i>
                                <span><span id="headerPatientName">Patient</span>'s <span id="headerTestName">Radiology</span> Report Details</span>
                            </h3>
                        </div>

                        <div class="card-body mt-3">
                            <div class="row">
                                <!-- Left Column: Patient Profile & Basic Info -->
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-sm-5 col-12 text-center margin-img mb-3">
                                            <p class="text-dark">
                                                <img id="file" class="userProfile" src="" width="180" height="180"
                                                    alt="report Image" style="border-radius:20px; object-fit: cover; box-shadow: 0 2px 8px rgba(0,0,0,0.08);"
                                                    onerror="this.onerror=null;this.src='{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}';">
                                            </p>
                                        </div>
                                        <div class="col-sm-7 col-12">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-user-md icon-style1"></i> Patient Name: </strong>
                                                <span class="info-value" id="patientName">N/A</span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-flask icon-style1"></i> Test Name: </strong>
                                                <span class="info-value" id="testName">N/A</span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-barcode icon-style1"></i> Test Code: </strong>
                                                <span class="info-value" id="testCode">N/A</span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fas fa-bone icon-style1"></i> Body Part: </strong>
                                                <span class="info-value" id="bodyPart">N/A</span>
                                            </p>
                                            <hr class="margin-text">
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column: Specs, Cost, Format & Report File -->
                                <div class="col-lg-6">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-calendar-check icon-style1"></i> Report Date: </strong>
                                        <span class="info-value" id="reportDate">N/A</span>
                                    </p>
                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-tag icon-style1"></i> Test Cost: </strong>
                                        <span class="info-value" id="testCost">N/A</span>
                                    </p>
                                    <hr class="margin-text">

                                    <p class="text-dark margin-text">
                                        <strong><i class="fas fa-file-alt icon-style1"></i> Report Format: </strong>
                                        <span class="info-value" id="reportFormat">N/A</span>
                                    </p>
                                    <hr class="margin-text">

                                    <div class="margin-text">
                                        <p class="mb-2"><strong><i class="fas fa-file-medical icon-style1"></i> Report File: </strong></p>
                                        <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                                            <a class="btn btn-outline-primary btn-sm" id="viewReportBtn" href="#" target="_blank" style="display:none; padding: 6px 14px; border-radius: 8px; font-weight: 500;">
                                                <i class="fas fa-file-alt mr-1"></i> View Uploaded
                                            </a>
                                            <span id="noFileText" class="text-muted" style="display:none; font-size: 13px;">No Uploaded File</span>
                                            <button type="button" class="btn btn-primary btn-sm download-report" data-id="{{ $radiology_report_id ?? '' }}" style="background-color: #cfece0; color: #0c4b33; border: 1px solid #b8dfd0; padding: 6px 14px; border-radius: 8px; font-weight: 600;">
                                                <i class="fa fa-download mr-1"></i> Download PDF
                                            </button>
                                        </div>
                                    </div>
                                    <hr class="margin-text">
                                </div>
                            </div>

                            <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                                @if(app('hasPermission')(22, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-test-btn"
                                        style="color:black; margin-right:10px">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span>
                                    </a>
                                @endif

                                @if(app('hasPermission')(22, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-reports" data-id="{{ $radiology_report_id ?? '' }}">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentReportId = "{{ $radiology_report_id ?? '' }}";

        function getIdFromUrl() {
            if (currentReportId) return currentReportId;
            const segments = window.location.pathname.split('/');
            return segments.pop() || segments.pop();
        }

        function getFileUrl(path) {
            if (!path) return '';
            if (path.startsWith('http://') || path.startsWith('https://')) return path;
            return '/' + path.replace(/^\/+/, '');
        }

        function renderReportData(report) {
            currentReportId = report.id;
            $('.download-report').attr('data-id', report.id);
            $('.delete-reports').attr('data-id', report.id);

            let pName = report.patient ? report.patient.fullname : 'N/A';
            let tName = report.test ? report.test.test_name : 'Radiology Test';

            $('#headerPatientName').text(pName);
            $('#headerTestName').text(tName);
            $('#patientName').text(pName);
            $('#testName').text(tName);
            $('#testCode').text(report.test?.test_code || 'N/A');
            $('#bodyPart').text(report.test?.body_part || 'N/A');
            $('#testCost').text(report.test?.cost ? ('₹' + parseFloat(report.test.cost).toFixed(2)) : 'N/A');
            $('#reportFormat').text(report.test?.report_format ? report.test.report_format.toUpperCase() : 'N/A');
            $('#reportDate').text(report.report_date || 'N/A');

            if (report.patient && report.patient.profile) {
                $('.userProfile').attr('src', report.patient.profile);
            } else {
                $('.userProfile').attr('src', "{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}");
            }

            const attachedFile = report.converted_image || report.report_file;
            if (attachedFile) {
                let fileUrl = getFileUrl(attachedFile);
                $('#viewReportBtn').attr('href', fileUrl).show();
                $('#noFileText').hide();
            } else {
                $('#viewReportBtn').hide();
                $('#noFileText').show();
            }

            $(".edit-test-btn").attr("href", "/radiology-reports/edit/" + report.id);
        }

        function showNA() {
            $('#patientName').text('N/A');
            $('#testName').text('N/A');
            $('#testCode').text('N/A');
            $('#bodyPart').text('N/A');
            $('#testCost').text('N/A');
            $('#reportFormat').text('N/A');
            $('#reportDate').text('N/A');
            $('#viewReportBtn').hide();
            $('#noFileText').show();
        }

        $(document).ready(function () {
            const id = getIdFromUrl();
            let token = localStorage.getItem("authToken");

            $.ajax({
                url: `/api/radiology-reports/${id}`,
                method: 'GET',
                dataType: 'json',
                headers: token ? { "Authorization": "Bearer " + token } : {},
                success: function (data) {
                    if (!data || $.isEmptyObject(data)) {
                        showNA();
                    } else {
                        renderReportData(data);
                    }
                },
                error: function () {
                    showNA();
                }
            });
        });

        // PDF Download handler
        $(document).on('click', '.download-report', function (e) {
            e.preventDefault();
            var reportId = $(this).attr('data-id') || getIdFromUrl();
            if (!reportId) {
                Swal.fire('Error', 'Report ID is missing!', 'error');
                return;
            }

            let token = localStorage.getItem("authToken");

            Swal.fire({
                title: 'Generating PDF...',
                text: 'Please wait while your radiology report PDF is being generated.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/api/radiology-reports/' + reportId + '/download', {
                method: 'GET',
                headers: token ? { 'Authorization': 'Bearer ' + token } : {}
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to download report PDF');
                }
                return response.blob();
            })
            .then(blob => {
                Swal.close();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = 'RadiologyReport_' + reportId + '.pdf';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            })
            .catch(error => {
                console.error('Download error:', error);
                Swal.fire('Error', 'Failed to generate PDF. Please try again.', 'error');
            });
        });

        $(document).on('click', '.delete-reports', function () {
            var testId = $(this).attr('data-id') || getIdFromUrl();
            if (!testId) {
                Swal.fire('Error', 'Test ID is missing!', 'error');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this radiology test?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    let token = localStorage.getItem("authToken");
                    $.ajax({
                        url: '/api/radiology-reports/' + testId,
                        type: 'DELETE',
                        headers: token ? { "Authorization": "Bearer " + token } : {},
                        success: function (response) {
                            Swal.fire('Deleted!', 'Radiology test deleted successfully!', 'success')
                                .then(() => {
                                    window.location.href = "{{ route('radiology-reports.index') }}";
                                });
                        },
                        error: function (xhr) {
                            Swal.fire('Error', 'Failed to delete radiology test.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endsection