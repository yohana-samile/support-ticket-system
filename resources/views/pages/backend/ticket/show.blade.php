@extends('layouts.backend.app')
@section('title', 'Ticket Details - ' . $ticket->ticket_number)
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Ticket Details Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Ticket #{{ $ticket->ticket_number }}</h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                 aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item" href="{{ route('backend.ticket.edit', $ticket->uid) }}">
                                    <i class="fas fa-edit fa-sm mr-2"></i> Edit Ticket
                                </a>
                                <form id="delete-ticket-form-{{ $ticket->uid }}" action="{{ route('backend.ticket.destroy', $ticket->uid) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item text-danger" onclick="confirmDelete('{{ $ticket->uid }}')">
                                        <i class="fas fa-trash fa-sm mr-2"></i> Delete Ticket
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h4 class="font-weight-bold">{{ $ticket->title }}</h4>
                        <div class="mb-4">
                            <span class="badge badge-{{ $ticket->status_badge }} mr-2">{{ ucfirst($ticket->status) }}</span>
                            <span class="badge badge-{{ $ticket->priority_badge }}">{{ ucfirst($ticket->priority) }}</span>
                        </div>

                        <div class="ticket-description mb-4">
                            {!! nl2br(e($ticket->description)) !!}
                        </div>

                        <!-- Attachments -->
                        @if($ticket->attachments->count() > 0)
                            <div class="attachments mb-4">
                                <h6 class="font-weight-bold">Attachments</h6>
                                <div class="row">
                                    @foreach($ticket->attachments as $attachment)
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body p-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="mr-3">
                                                            @if(in_array($attachment->mime_type, ['image/jpeg', 'image/png']))
                                                                <img src="{{ route('backend.attachment.view', $attachment->uid) }}" alt="{{ $attachment->original_name }}" class="img-thumbnail" style="max-height: 50px;">
                                                            @else
                                                                <i class="fas fa-file-alt fa-2x text-secondary"></i>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <small class="d-block text-truncate" style="max-width: 150px;">{{ $attachment->original_name }}</small>
                                                            <small class="text-muted">{{ formatBytes($attachment->size) }}</small>
                                                            <a href="{{ route('backend.attachment.download', $attachment->uid) }}" download class="d-block" title="Download">
                                                                <i class="fas fa-download fa-xs"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Ticket Meta -->
                        <div class="ticket-meta border-top pt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Created</small>
                                    <p>{{ $ticket->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Last Updated</small>
                                    <p>{{ $ticket->updated_at->format('M d, Y h:i A') }}</p>
                                </div>
                                @if($ticket->due_date)
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Due Date</small>
                                        <p class="{{ $ticket->due_date->isBefore(today()) ? 'text-danger' : '' }}">
                                            {{ $ticket->due_date->format('M d, Y') }}
                                            @if($ticket->due_date->isBefore(today()))
                                                <span class="badge badge-danger">Overdue</span>
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Comments</h6>
                    </div>
                    <div class="card-body">
                        @foreach($ticket->comments as $comment)
                            <div class="mb-4 comment {{ $loop->last ? '' : 'border-bottom pb-4' }}">
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="font-weight-bold">{{ $comment->user->name }}</div>
                                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="comment-content">
                                    {!! nl2br(e($comment->content)) !!}
                                </div>
                            </div>
                        @endforeach

                        <!-- Add Comment Form -->
                        <form action="{{ route('backend.comment.store', $ticket->uid) }}" method="POST" class="mt-4">
                            @csrf
                            <div class="form-group">
                                <label for="comment">Add Comment</label>
                                <textarea name="content" id="comment" rows="3" class="form-control" required></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-comment mr-2"></i> Post Comment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Ticket Info Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Ticket Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Category</small>
                            <p>{{ $ticket->category->name }}</p>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Reported By</small>
                            <p>{{ $ticket->reported_customer }}</p>
                        </div>

                        @if($ticket->assigned_to)
                            <div class="mb-3">
                                <small class="text-muted d-block">Assigned To</small>
                                <p>{{ $ticket->assignedTo->name }}</p>
                            </div>
                        @endif

                        @if($ticket->time_solved)
                            <div class="mb-3">
                                <small class="text-muted d-block">Resolved On</small>
                                <p>{{ $ticket->time_solved->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Status Update Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Update Status</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('backend.ticket.update-status', $ticket->uid) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <select name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                        </form>

                        @if(isAdmin() || auth()->user()->is_staff)
                            <form action="{{ route('backend.ticket.reassign', $ticket->uid) }}" method="POST" class="mt-3">
                                @csrf
                                <div class="form-group">
                                    <label for="assigned_to">Reassign Ticket</label>
                                    <select name="assigned_to" id="assigned_to" class="form-control">
                                        <option value="">Unassigned</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $ticket->assigned_to == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary float-right">Reassign</button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Activity Log -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Activity Log</h6>
                    </div>
                    <div class="card-body scrollable-activity-log">
                        <ul class="list-group list-group-flush">
                            @foreach($ticket->activities ?? [] as $activity)
                                <li class="list-group-item px-0 py-2">
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                    <div>{{ $activity->description }}</div>
                                    <small class="text-muted">by {{ $activity->causer->name ?? 'System' }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .scrollable-activity-log {
            max-height: 250px;
            overflow-y: auto;
        }

        .ticket-description {
            white-space: pre-wrap;
            line-height: 1.6;
        }
        .comment-content {
            white-space: pre-wrap;
            line-height: 1.6;
        }
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

        // Auto-resize textareas
        document.querySelectorAll('textarea').forEach(el => {
            el.style.height = el.setAttribute('style', 'height: ' + el.scrollHeight + 'px');
            el.addEventListener('input', e => {
                el.style.height = 'auto';
                el.style.height = (el.scrollHeight) + 'px';
            });
        });
    </script>
@endpush
