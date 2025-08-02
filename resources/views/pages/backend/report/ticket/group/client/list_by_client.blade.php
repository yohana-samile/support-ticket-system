@extends('layouts.backend.app')
@section('title', __('label.client'))
@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-light">
            <h6 class="m-0 font-weight-bold card-header-title">{{ $title }}</h6>
            <div>
                <span class="badge badge-light mr-2">
                    <i class="fas fa-ticket-alt"></i> Total: <span>{{ $total_tickets ?? 0 }}</span>
                </span>
                <button class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#reportFilterModal">
                    <i class="fas fa-filter"></i> {{ __('label.filter') }}
                </button>
                <div class="btn-group" id="exportControls">
                    <button class="btn btn-sm btn-outline-success export-action"
                            id="exportExcelBtn"
                            data-type="excel"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Export to Excel">
                        <i class="fas fa-file-excel"></i>
                    </button>

                    <!-- PDF Export Button with Tooltip -->
                    <button class="btn btn-sm btn-outline-danger export-action"
                            id="exportPdfBtn"
                            data-type="pdf"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Export to PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>

                    <!-- Export Options Dropdown -->
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary dropdown-toggle"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Export options">
                        <i class="fas fa-cog"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><h6 class="dropdown-header">Export Options</h6></li>
                        <li><a class="dropdown-item" href="#" id="exportCurrentView"><i class="fas fa-table me-2"></i> Current View</a></li>
                        <li><a class="dropdown-item" href="#" id="exportAllData"><i class="fas fa-database me-2"></i> All Data (All Clients)</a></li>
{{--                        <li><hr class="dropdown-divider"></li>--}}
{{--                        <li><a class="dropdown-item" href="#" id="exportSettings"><i class="fas fa-sliders-h me-2"></i>Advanced Options</a></li>--}}
                    </ul>
                </div>

            </div>
        </div>
        <div class="card-body">
            <select class="form-control mb-3 select2-ajax" id="clientSelector"
                    data-placeholder="Search for a Client..."
                    data-ajax-url="{{ route('backend.client.search') }}" style="width: 300px;">
                @if(isset($client_selected))
                    <option value="{{ $client_uid }}" selected>{{ $client_selected }}</option>
                @endif
            </select>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="listTicketsByClientTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>{{__('label.client')}}</th>
                            <th>{{__('label.subject')}}</th>
                            <th>{{__('label.topic')}}</th>
                            <th>{{__('label.subtopic')}}</th>
                            <th>{{__('label.reported_by')}}</th>
                            <th>{{__('label.priority')}}</th>
                            <th>{{__('label.status')}}</th>
                            <th>{{__('label.created_at')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('includes.partials.filter_modal')
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            function loadTickets(client) {
                if ($.fn.DataTable.isDataTable('#listTicketsByClientTable')) {
                    table.destroy();
                }

                table = $('#listTicketsByClientTable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: "{{ route('backend.report.list_by_client', '__uid__') }}".replace('__uid__', client),
                        data: function (d) {
                            d.start_date = $('#startDate').val();
                            d.end_date = $('#endDate').val();
                        }
                    },
                    columns: [
                        { data: 'client.name', defaultContent: '' },
                        { data: 'title' },
                        { data: 'topic.name', defaultContent: '' },
                        { data: 'subtopic.name', defaultContent: '' },
                        { data: 'user.name', defaultContent: '' },
                        { data: 'priority_badge' },
                        { data: 'status_badge' },
                        { data: 'when_reported' },
                    ]
                });
            }

            let initialUid = $('#clientSelector').val();
            loadTickets(initialUid);

            $('#clientSelector').on('change', function () {
                let client = $(this).val();
                if (client) {
                    loadTickets(client);
                }
            });

            $('.select2-ajax').select2({
                ajax: {
                    url: $('.select2-ajax').data('ajax-url'),
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.data.map(item => ({
                                id: item.uid,
                                text: item.name
                            })),
                            pagination: {
                                more: data.next_page_url
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: $('.select2-ajax').data('placeholder'),
                allowClear: true,
                escapeMarkup: function(markup) { return markup; }
            });


            /**
             * export
             */
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Set default export type and scope
            let exportType = 'excel';
            let exportScope = 'current'; // 'current' or 'all'

            // Highlight default button
            $('#exportExcelBtn').addClass('active');

            // Handle export type selection
            $('.export-action').on('click', function() {
                exportType = $(this).data('type');

                // Update button states
                $('.export-action').removeClass('active');
                $(this).addClass('active');

                // Execute export with current scope
                performExport();
            });

            // Handle export scope selection
            $('#exportCurrentView').on('click', function(e) {
                e.preventDefault();
                exportScope = 'current';
                performExport();Topic
            });

            $('#exportAllData').on('click', function(e) {
                e.preventDefault();
                exportScope = 'all';
                performExport();
            });

            // Main export function
            function performExport() {
                let uid = exportScope === 'current' ? $('#clientSelector').val() : 'all';
                let start_date = $('#startDate').val();
                let end_date = $('#endDate').val();

                if (!uid && exportScope === 'current') {
                    toastr.info('Please select a client first', '', {
                        timeOut: 3000,
                        positionClass: 'toast-bottom-right'
                    });
                    return;
                }

                // Show loading state
                const btn = $(`.export-action[data-type="${exportType}"]`);
                const originalHtml = btn.html();
                btn.html('<i class="fas fa-spinner fa-spin"></i>');

                // Prepare export URL
                const url = "{{ route('backend.report.export_ticket_by_client') }}" +
                    '?client=' + encodeURIComponent(uid) +
                    '&start_date=' + encodeURIComponent(start_date) +
                    '&end_date=' + encodeURIComponent(end_date) +
                    '&type=' + encodeURIComponent(exportType) +
                    '&scope=' + encodeURIComponent(exportScope);

                // Use hidden iframe for download to avoid page navigation
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = url;
                document.body.appendChild(iframe);

                // Reset button after delay
                setTimeout(() => {
                    btn.html(originalHtml);
                    document.body.removeChild(iframe);

                    // Show success notification
                    toastr.success(`Exporting ${exportScope === 'all' ? 'all clients data' : 'current view'} as ${exportType.toUpperCase()}`, 'Export Started', {
                        timeOut: 3000,
                        positionClass: 'toast-bottom-right'
                    });
                }, 2000);
            }
            //
            // // Settings handler
            // $('#exportSettings').on('click', function(e) {
            //     e.preventDefault();
            //     // You could implement a modal with advanced options here
            //     toastr.info('there is no Advanced export options Settings for now', {
            //         timeOut: 3000,
            //         positionClass: 'toast-bottom-right'
            //     });
            // });
        });
    </script>
@endpush
