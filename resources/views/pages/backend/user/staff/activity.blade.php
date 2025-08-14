@extends('layouts.backend.app')
@section('title', __('label.user_caused_activity'))

@section('content')
    <div class="container-fluid">
        <div id="content">
            <div class="card shadow-sm border-0 rounded">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Activity Log for {{ $user->name }}</h4>
                        <a href="{{ route('backend.user.show', $user->uid) }}" class="btn btn-sm btn-white">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Profile
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0" id="caused_activity-table">
                            <thead>
                                <tr>
                                    <th width="30%">{{__('label.description')}}</th>
                                    <th width="25%">{{__('label.subject')}}</th>
                                    <th width="25%">{{__('label.changes')}}</th>
                                    <th width="20%">{{__('label.date')}}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for viewing changes -->
    <div class="modal fade" id="changesModal" tabindex="-1" role="dialog" aria-labelledby="changesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changesModalLabel">Activity Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Old Values</h6>
                            <pre id="old-values" class="bg-light p-3 rounded"></pre>
                        </div>
                        <div class="col-md-6">
                            <h6>New Values</h6>
                            <pre id="new-values" class="bg-light p-3 rounded"></pre>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .badge-created { background-color: #28a745; }
        .badge-updated { background-color: #17a2b8; }
        .badge-deleted { background-color: #dc3545; }
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            font-size: 14px;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            const userUid = '{{ $user->uid }}';

            const table = $('#caused_activity-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: `{{ route('backend.user.get_caused_activity_for_dt', ['user' => '__USER__']) }}`.replace('__USER__', userUid),
                    type: 'GET'
                },
                columns: [
                    {
                        data: 'description',
                        name: 'description',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'subject',
                        name: 'subject',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'changes',
                        name: 'changes',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'date',
                        name: 'created_at',
                        orderable: true,
                        searchable: false
                    }
                ],
                order: [[3, 'desc']], // Order by date (4th column) descending
                dom: "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3],
                            format: {
                                body: function (data, row, column, node) {
                                    // Strip HTML tags for export
                                    return $(data).text() || data;
                                }
                            }
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-info btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3],
                            format: {
                                body: function (data, row, column, node) {
                                    // Strip HTML tags for print
                                    return $(data).text() || data;
                                }
                            }
                        }
                    }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search activities...",
                    lengthMenu: "Show _MENU_ activities per page",
                    zeroRecords: "No matching activities found",
                    info: "Showing _START_ to _END_ of _TOTAL_ activities",
                    infoEmpty: "No activities available",
                    infoFiltered: "(filtered from _MAX_ total activities)"
                }
            });

            // Handle view changes button click (using event delegation)
            $('#caused_activity-table').on('click', '.view-changes', function() {
                try {
                    const propertiesStr = $(this).attr('data-properties');
                    if (!propertiesStr) {
                        throw new Error('No properties data found');
                    }

                    const properties = JSON.parse(propertiesStr);
                    const oldValues = properties.old || {};
                    const newValues = properties.attributes || {};

                    $('#old-values').html(JSON.stringify(oldValues, null, 2));
                    $('#new-values').html(JSON.stringify(newValues, null, 2));

                    $('#changesModal').modal('show');
                } catch (error) {
                    const toastOptions = {
                        timeOut: 3000,
                        positionClass: 'toast-bottom-right',
                        closeButton: true
                    };
                    toastr.error('Failed to load changes data', '', toastOptions);
                }
            });

            // Disable buttons when no data
            table.on('draw', function() {
                const hasData = table.rows({ filter: 'applied' }).count() > 0;
                table.buttons().enable(hasData);
            });
        });
    </script>
@endpush
