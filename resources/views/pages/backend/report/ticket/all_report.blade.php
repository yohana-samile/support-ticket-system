@extends('layouts.backend.app')
@section('title', __('label.report_summary'))
@section('content')
    <div class="container-fluid">
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
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="allReportTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('Ticket ID') }}</th>
                                <th>{{ __('label.client') }}</th>
                                <th>{{ __('label.ticket_about') }}</th>
                                <th>{{ __('label.topic') }}</th>
                                <th>{{ __('label.status') }}</th>
                                <th>{{ __('label.priority') }}</th>
                                <th>{{ __('Assigned To') }}</th>
                                <th>{{ __('Reported At') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        <!-- Data loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('includes.partials.filter_modal')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            table = $('#allReportTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('backend.ticket.get_ticket_for_dt') }}",
                    type: 'GET',
                    data: function(d) {
                        d.start_date = $('#startDate').val();
                        d.end_date = $('#endDate').val();
                        d.cutoff_period = $('select[name="cutoff_period"]').val();
                        // Pass any additional filters
                    }
                },
                columns: [
                    { data: 'ticket_number', name: 'ticket_number' },
                    { data: 'reported_by', name: 'client.name' },
                    { data: 'title', name: 'title' },
                    { data: 'topic_name', name: 'topic.name' },
                    { data: 'status_badge', name: 'status' },
                    { data: 'priority_badge', name: 'priority' },
                    { data: 'assigned_to', name: 'assignedTo.name' },
                    { data: 'when_reported', name: 'created_at' },
                ],
                order: [[7, 'desc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                }
            });
        });
    </script>
@endpush
