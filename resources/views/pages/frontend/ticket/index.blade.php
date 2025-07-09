@extends('layouts.backend.app')
@section('title', 'Tickets')
@section('content')
    <div id="content">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-end mb-4">
                @unless(auth()->user()->is_reporter)
                    <a href="{{ route('backend.ticket.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                        <i class="fas fa-plus fa-sm text-white-50"></i> Create New Ticket
                    </a>
                @endunless
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        @if(auth()->user()->is_reporter)
                            My Tickets
                        @else
                            All Tickets
                        @endif
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
                            @unless(auth()->user()->is_reporter)
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('backend.ticket.assigned') }}">My Assigned Tickets</a>
                            @endunless
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Ticket ID</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Created</th>
                                @unless(auth()->user()->is_reporter)
                                    <th>Requester</th>
                                    <th>Assignee</th>
                                @endunless
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket->uid }}</td>
                                    <td>
                                        <a href="{{ route('tickets.show', $ticket->id) }}">
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
                                            @if($ticket->assignee)
                                                {{ $ticket->assignee->name }}
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                    @endunless
                                    <td>
                                        <a href="{{ route('backend.ticket.show', $ticket->id) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $ticket)
                                            <a href="{{ route('backend.ticket.edit', $ticket->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $ticket)
                                            <form action="{{ route('backend.ticket.destroy', $ticket->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
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
    </script>
@endpush
