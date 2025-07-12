@extends('layouts.backend.app')
@section('title', 'Tickets')
@section('content')
    <div class="container-fluid">
        <div id="content">
                <div class="d-sm-flex align-items-center justify-content-end mb-4">
                    <a href="{{ route('backend.ticket.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                        <i class="fas fa-plus fa-sm text-white-50"></i> Create New Ticket
                    </a>
                </div>

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
                            <table class="table table-bordered" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>Ticket ID</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Reported</th>
                                        <th>Requester</th>
                                        <th>Assignee</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                        <tr>
                                            <td>{{ $ticket->ticket_number }}</td>
                                            <td>
                                                <a href="{{ route('backend.ticket.show', $ticket->uid) }}">
                                                    {{ Str::limit($ticket->title, 30) }}
                                                </a>
                                            </td>
                                            <td>{{ $ticket->category->name }}</td>
                                            <td>
                                            <span class="badge badge-{{ $ticket->status_badge }}">
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                            </td>
                                            <td>
                                            <span class="badge badge-{{ $ticket->priority_badge }}">
                                                {{ ucfirst($ticket->priority) }}
                                            </span>
                                            </td>
                                            <td>{{ $ticket->created_at->diffForHumans() }}</td>
                                            @unless(auth()->user()->is_reporter)
                                                <td>{{ $ticket->user->name }}</td>
                                                <td>
                                                    @if($ticket->assignedTo)
                                                        {{ $ticket->assignedTo->name }}
                                                    @else
                                                        <span class="text-muted">Unassigned</span>
                                                    @endif
                                                </td>
                                            @endunless
                                            <td>
                                                <a href="{{ route('backend.ticket.show', $ticket->uid) }}" class="text-info mr-2 text-decoration-none" title="View">
                                                    <i class="fas fa-eye fa-sm"></i>
                                                </a>

                                                @if($ticket->status !== 'closed')
                                                    <a href="{{ route('backend.ticket.edit', $ticket->uid) }}" class="text-primary mr-2 text-decoration-none" title="Edit">
                                                        <i class="fas fa-edit fa-sm"></i>
                                                    </a>

                                                    <a href="javascript:void(0);" class="text-danger mr-2 text-decoration-none" title="Delete" onclick="confirmDelete('{{ $ticket->uid }}')">
                                                        <i class="fas fa-trash fa-sm"></i>
                                                    </a>

                                                    <form id="delete-ticket-form-{{ $ticket->uid }}" action="{{ route('backend.ticket.destroy', $ticket->uid) }}" method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Ticket ID</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Reported</th>
                                        <th>Requester</th>
                                        <th>Assignee</th>
                                        <th>Actions</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $tickets->links() }}
                        </div>
                    </div>
                </div>

        </div>
    </div>
@endsection

@push('styles')
    <style>
        .badge-open { background-color: #f6c23e; color: #000; }
        .badge-in_progress { background-color: #36b9cc; color: #fff; }
        .badge-resolved { background-color: #1cc88a; color: #fff; }
        .badge-closed { background-color: #e74a3b; color: #fff; }
        .badge-low { background-color: #858796; color: #fff; }
        .badge-medium { background-color: #4e73df; color: #fff; }
        .badge-high { background-color: #f6c23e; color: #000; }
        .badge-critical { background-color: #e74a3b; color: #fff; }
    </style>
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

        document.addEventListener('DOMContentLoaded', function() {
            $(document).ready(function() {
                $('#dataTable').DataTable({
                    "paging": false,
                    "searching": true,
                    "info": false,
                    "ordering": true,
                    "columnDefs": [
                        { "orderable": false, "targets": -1 } // Disable sorting for actions column
                    ]
                });
            });
        });
    </script>
@endpush
