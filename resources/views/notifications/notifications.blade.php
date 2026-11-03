@extends('layout.app')
@section('content')
    <style>
        .badge-bg {
            background-color: #F89884;
            color: #fff;
            font-weight: 600;
        }

        .hdr-notif-btn {
            background-color: #fed9cf !important;
            color: #000000 !important;
            border-radius: 20px !important;
            font-weight: 500 !important;
            font-size: 13px !important;
            padding: 6px 16px !important;
            border: none !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            cursor: pointer !important;
            transition: all 0.2s ease-in-out !important;
        }
        .hdr-notif-btn:hover {
            background-color: #f7c3b4 !important;
            color: #000000 !important;
        }

        .action-circle-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            margin: 0 3px;
            font-size: 13px;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            text-decoration: none;
        }

        .action-view-btn {
            background-color: #fed9cf;
            color: #f89884;
        }
        .action-view-btn:hover {
            background-color: #f89884;
            color: #ffffff;
        }

        .action-delete-btn {
            background-color: #fed9cf;
            color: #f89884;
        }
        .action-delete-btn:hover {
            background-color: #e55353;
            color: #ffffff;
        }

        .swal-confirm-btn {
            color: rgb(58, 58, 58) !important;
            background-color: #cfece0 !important;
            border-radius: 20px !important;
        }
        .swal-cancel-btn {
            color: white !important;
            background-color: #f89884 !important;
            border-radius: 20px !important;
        }

        /* Spacing between header and table */
        .notifications-card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            background: #ffffff;
            overflow: hidden;
        }
        .notifications-card .card-header {
            padding: 0 24px !important;
            border-bottom: none !important;
        }
        .notifications-card .card-body {
            padding: 0 24px 30px 24px !important;
        }
        .notifications-card .table-responsive {
            margin-top: 10px !important;
        }
        .notifications-card table#notificationTbl {
            margin-top: 10px !important;
            width: 100% !important;
        }

        /* Controls styling like reference image */
        div.dataTables_wrapper div.dataTables_length,
        div.dt-container div.dt-length {
            float: left;
            margin-bottom: 15px !important;
        }
        div.dataTables_wrapper div.dataTables_filter,
        div.dt-container div.dt-search {
            float: right;
            text-align: right;
            margin-bottom: 15px !important;
        }
        div.dataTables_wrapper div.dataTables_length select,
        div.dt-container div.dt-length select {
            border-radius: 20px !important;
            border: 1.5px solid #f89884 !important;
            padding: 4px 12px !important;
            outline: none !important;
            background-color: #fff !important;
        }
        div.dataTables_wrapper div.dataTables_filter input,
        div.dt-container div.dt-search input {
            border-radius: 20px !important;
            border: 1.5px solid #f89884 !important;
            padding: 5px 14px !important;
            outline: none !important;
            background-color: #fff !important;
            min-width: 180px;
        }
    </style>
    <div class="page-wrapper">
        <div class="content" style="height:100vh">

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card notifications-card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="background-color:#f89884; gap: 10px;">
                            <h3 class="card-title text-white mb-0">
                                <i class="fa fa-bell px-2" style="font-size:20px"></i>All Notifications
                            </h3>
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                <button type="button" class="btn hdr-notif-btn" id="markAllAsReadBtn">
                                    <i class="fas fa-check"></i> Mark All as Read
                                </button>
                                <button type="button" class="btn hdr-notif-btn" id="deleteAllNotificationsBtn">
                                    <i class="fas fa-trash"></i> Delete All
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="margin-top: 15px !important;">
                                <div id="demo_info" class="box"></div>
                                <table id="notificationTbl" class="table custom-table" style="margin-top: 12px !important;">
                                    <thead style="background-color:#ff8e29;">
                                        <tr>
                                            <th>Message</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($notifications && $notifications->isNotEmpty())
                                            @foreach ($notifications as $notification)
                                                @php
                                                    $module = $notification->info['module'] ?? '';
                                                    $moduleId = $notification->info['module_id'] ?? '';
                                                    $href = '#'; // Default

                                                    if ($module === 'appointment') {
                                                        $href = route('appointment.show', $moduleId);
                                                    } elseif ($module === 'dailydata') {
                                                        $href = route('dailydata.show', $moduleId);
                                                    } elseif ($module === 'followup') {
                                                        $href = route('followup.show', $moduleId);
                                                    } else {
                                                        $href = url()->current();
                                                    }
                                                @endphp
                                                <tr id="notif-row-{{ $notification->id }}">
                                                    <td>{{ $notification->info['message'] ?? 'New Message' }}
                                                        @if (!$notification->is_read)
                                                            <span class="badge badge-bg ms-2">New</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ optional($notification->created_at)->format('d/m/Y') ?? '' }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <a href="{{ $href }}"
                                                                class="notification-link1 action-circle-btn action-view-btn"
                                                                title="View"
                                                                data-id="{{ $notification->id }}"
                                                                data-url="{{ route('notification.update', $notification->id) }}">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="action-circle-btn action-delete-btn delete-single-btn"
                                                                title="Delete"
                                                                data-id="{{ $notification->id }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script>
        $(document).ready(function() {
            if ($.fn && $.fn.DataTable) {
                if ($.fn.DataTable.isDataTable('#notificationTbl')) {
                    $('#notificationTbl').DataTable().destroy();
                }
                $('#notificationTbl').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    pageLength: 10,
                    destroy: true
                });
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            // View single notification
            $(document).on('click', '.notification-link1', async function(e) {
                e.preventDefault();

                var notificationId = $(this).data('id');
                var updateUrl = $(this).data('url');
                var redirectUrl = $(this).attr('href');
                var $button = $(this);

                $button.prop('disabled', true);
                var btnHtml = '<span class="spinner-border spinner-border-sm Btnloader" role="status" aria-hidden="true"></span>';
                $button.append(btnHtml);

                $.ajax({
                    url: updateUrl,
                    method: 'POST',
                    data: {
                        _method: 'POST',
                        read: true
                    },
                    success: function(response) {
                        $button.prop('disabled', false);
                        $button.find('.Btnloader').remove();

                        if (redirectUrl && redirectUrl !== '#' && redirectUrl !== window.location.href) {
                            window.location.href = redirectUrl;
                        } else {
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error while updating notification:', xhr.responseText);
                        if (redirectUrl && redirectUrl !== '#' && redirectUrl !== window.location.href) {
                            window.location.href = redirectUrl;
                        } else {
                            location.reload();
                        }
                    },
                    complete: function() {
                        $button.prop('disabled', false);
                        $button.find('.Btnloader').remove();
                    }
                });
            });

            // Single Delete functionality
            $(document).on('click', '.delete-single-btn', function(e) {
                e.preventDefault();
                let notifId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to delete this notification?',
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
                            url: "{{ url('notifications') }}/" + notifId,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message || 'Notification deleted successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message || 'Failed to delete notification.', 'error');
                            }
                        });
                    }
                });
            });

            // Mark All as Read functionality
            $('#markAllAsReadBtn').on('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Mark all as read?',
                    text: 'Are you sure you want to mark all notifications as read?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#cfece0',
                    cancelButtonColor: '#f89884',
                    confirmButtonText: 'Yes, mark all!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'swal-confirm-btn',
                        cancelButton: 'swal-cancel-btn'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('notification.markAllAsRead') }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message || 'All notifications marked as read.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message || 'Failed to mark notifications as read.', 'error');
                            }
                        });
                    }
                });
            });

            // Delete All functionality
            $('#deleteAllNotificationsBtn').on('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Delete all notifications?',
                    text: 'This will delete all your notifications. This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#cfece0',
                    cancelButtonColor: '#f89884',
                    confirmButtonText: 'Yes, delete all!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'swal-confirm-btn',
                        cancelButton: 'swal-cancel-btn'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('notification.deleteAll') }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message || 'All notifications deleted successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message || 'Failed to delete all notifications.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
