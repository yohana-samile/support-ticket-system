@extends('layouts.backend.app')
@section('title', __('label.tickets_by_payment_channel'))
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
                <button class="btn btn-sm btn-outline-success" id="exportData">
                    <i class="fas fa-file-export"></i> {{ __('label.export') }}
                </button>
            </div>
        </div>
        <div class="card-body">
            <select id="saasSelector" class="form-control mb-3" style="width: 300px;">
                @foreach($saas as $app)
                    <option value="{{ $app->uid }}" {{ request()->route('saas') == $app->uid ? 'selected' : '' }}>
                        {{ $app->name }}
                    </option>
                @endforeach
            </select>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="listTicketsBySaasAppTable" width="100%" cellspacing="0">
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

@push('scripts')
    <script>
        $(document).ready(function () {
            function loadTickets(uid) {
                if ($.fn.DataTable.isDataTable('#listTicketsBySaasAppTable')) {
                    table.destroy();
                }

                table = $('#listTicketsBySaasAppTable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: "{{ route('backend.report.list_by_saas_app', '__uid__') }}".replace('__uid__', uid),
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

            // Initial load
            let initialUid = $('#saasSelector').val();
            loadTickets(initialUid);

            // Reload when selector changes
            $('#saasSelector').on('change', function () {
                let uid = $(this).val();
                loadTickets(uid);
            });
        });

    </script>
@endpush
