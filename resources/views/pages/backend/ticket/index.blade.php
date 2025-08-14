@extends('layouts.backend.app')
@section('title', __('label.ticket'))
@section('content')
    <div class="container-fluid">
        <div id="content">
            @if(access()->allow('create_tickets'))
                <div class="d-sm-flex align-items-center justify-content-end mb-4">
                    <a href="{{ route('backend.ticket.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                        <i class="fas fa-plus fa-sm text-white-50"></i> {{__('label.create')}}
                    </a>
                </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        All Tickets
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                             aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Filter Options:</div>
                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'open']) }}">Open Tickets</a>
                            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'resolved']) }}">Resolved Tickets</a>
                            <a class="dropdown-item" href="{{ route('backend.ticket.index') }}">All Tickets</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="ticketTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Title</th>
                                    <th>Topic</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Reported</th>
                                    <th>Requester</th>
                                    <th>Assigned_to</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
@endpush

@push('scripts')
    <script>
        function confirmDelete(ticketId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-ticket-form-${ticketId}`).submit();
                }
            });
        }

        $(document).ready(function() {
            $('#ticketTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('backend.ticket.get_ticket_for_dt') }}",
                    type: 'GET',
                    data: function(d) {
                        // Pass any additional filters
                        d.status = "{{ request('status') }}";
                    }
                },
                columns: [
                    { data: 'ticket_number', name: 'ticket_number' },
                    { data: 'title', name: 'title' },
                    { data: 'topic_name', name: 'topic.name' },
                    { data: 'status_badge', name: 'status' },
                    { data: 'priority_badge', name: 'priority' },
                    { data: 'when_reported', name: 'created_at' },
                    { data: 'reported_by', name: 'client.name' },
                    { data: 'assigned_to', name: 'assignedTo.name' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[5, 'desc']], // Order by created_at
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                }
            });
        });
    </script>
@endpush
