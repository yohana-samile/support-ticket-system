@extends('layouts.frontend.app')
@section('title', 'Ticket Details - ' . $ticket->ticket_number)
@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-end mb-4">
            <div class="d-flex">
                @if($ticket->status === 'resolved')
                    <a href="{{ route('frontend.ticket.feedback', $ticket->uid) }}" class="btn btn-sm btn-success mr-2">
                        <i class="fas fa-clipboard-check mr-1"></i> Submit Feedback
                    </a>
                @endif
                @if($ticket->status === 'open')
                    <a href="{{ route('frontend.ticket.edit', $ticket->uid) }}" class="btn btn-sm btn-primary mr-2">
                        <i class="fas fa-edit fa-sm mr-1"></i> Edit Ticket
                    </a>
                    <form id="delete-ticket-form-{{ $ticket->uid }}" action="{{ route('frontend.ticket.destroy', $ticket->uid) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $ticket->uid }}')">
                            <i class="fas fa-trash fa-sm mr-1"></i> Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Ticket Details Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex align-items-center justify-content-between bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Ticket Details</h6>
                        <div class="d-flex">
                            <span class="badge badge-light mr-2">{{ ucfirst($ticket->status) }}</span>
                            <span class="badge badge-light">{{ ucfirst($ticket->priority) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h4 class="font-weight-bold text-gray-800 mb-4">{{ $ticket->title }}</h4>

                        <div class="ticket-description mb-4 p-3 bg-light rounded">
                            {!! nl2br(e($ticket->description)) !!}
                        </div>

                        <!-- Attachments Section -->
                        @if($ticket->attachments->count() > 0)
                            <div class="attachments mb-4">
                                <h5 class="font-weight-bold text-gray-800 mb-3">
                                    <i class="fas fa-paperclip mr-2"></i>Attachments ({{ $ticket->attachments->count() }})
                                </h5>
                                <div class="row">
                                    @foreach($ticket->attachments as $attachment)
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-body p-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="mr-3">
                                                            @if(in_array($attachment->mime_type, ['image/jpeg', 'image/png']))
                                                                <img src="{{ route('backend.attachment.view', $attachment->uid) }}"
                                                                     alt="{{ $attachment->original_name }}"
                                                                     class="img-thumbnail"
                                                                     style="max-height: 50px;">
                                                            @else
                                                                <i class="fas fa-file-alt fa-2x text-secondary"></i>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <small class="d-block text-truncate font-weight-bold" style="max-width: 150px;">
                                                                {{ $attachment->original_name }}
                                                            </small>
                                                            <small class="text-muted">{{ formatBytes($attachment->size) }}</small>
                                                            <div class="mt-1">
                                                                <a href="{{ route('backend.attachment.download', $attachment->uid) }}"
                                                                   download
                                                                   class="btn btn-sm btn-outline-primary btn-block">
                                                                    <i class="fas fa-download fa-xs mr-1"></i> Download
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Metadata Section -->
                        <div class="ticket-meta border-top pt-3">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card border-left-primary h-100">
                                        <div class="card-body">
                                            <h6 class="text-xs font-weight-bold text-primary text-uppercase mb-1">Created</h6>
                                            <p class="mb-0">{{ $ticket->created_at->format('M d, Y h:i A') }}</p>
                                            <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card border-left-info h-100">
                                        <div class="card-body">
                                            <h6 class="text-xs font-weight-bold text-info text-uppercase mb-1">Last Updated</h6>
                                            <p class="mb-0">{{ $ticket->updated_at->format('M d, Y h:i A') }}</p>
                                            <small class="text-muted">{{ $ticket->updated_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                                @if($ticket->due_date)
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-left-{{ $ticket->due_date->isBefore(today()) ? 'danger' : 'success' }} h-100">
                                            <div class="card-body">
                                                <h6 class="text-xs font-weight-bold text-uppercase mb-1">
                                                    Due Date
                                                    @if($ticket->due_date->isBefore(today()))
                                                        <span class="badge badge-danger float-right">Overdue</span>
                                                    @endif
                                                </h6>
                                                <p class="mb-0">{{ $ticket->due_date->format('M d, Y') }}</p>
                                                <small class="text-muted">
                                                    @if($ticket->due_date->isFuture())
                                                        Due in {{ $ticket->due_date->diffForHumans() }}
                                                    @else
                                                        Overdue by {{ $ticket->due_date->diffForHumans() }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-comments mr-2"></i>Comments
                        </h6>
                        <div class="comment-sort">
                            <select class="form-control form-control-sm" id="comment-sort">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="comments-container" class="mb-4">
                            @if($ticket->comments->isEmpty())
                                <div class="text-center py-4">
                                    <i class="fas fa-comment-slash fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-muted">No comments yet. Be the first to add one!</p>
                                </div>
                            @else
                                @foreach($ticket->comments as $comment)
                                    <div class="mb-4 comment comment-item {{ $loop->last ? '' : 'border-bottom pb-4' }}">
                                        <div class="d-flex mb-3">
                                            <img src="{{ $comment->user->profile_photo_path ?? asset('/asset/img/undraw_profile.svg') }}"
                                                 alt="{{ $comment->user->name }}"
                                                 class="rounded-circle mr-3"
                                                 width="40"
                                                 height="40">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="font-weight-bold mb-0">{{ $comment->user->name }}</h6>
                                                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                                </div>
                                                <div class="comment-content p-3 bg-light rounded">
                                                    {!! nl2br(e($comment->content)) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Comment Form -->
                        <form id="comment-form" action="{{ route('backend.comment.store', $ticket->uid) }}" method="POST" class="mt-4">
                            @csrf
                            <div class="form-group">
                                <label for="comment" class="font-weight-bold">Add Comment</label>
                                <textarea name="content" id="comment" rows="3" class="form-control"
                                          placeholder="Type your comment here..." required></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane mr-2"></i> Post Comment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar Column -->
            <div class="col-lg-4">
                <!-- Ticket Information Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-info-circle mr-2"></i>Ticket Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-gray-800 mb-3">Details</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0 py-2 d-flex justify-content-between">
                                    <span class="font-weight-bold">Category:</span>
                                    <span>{{ $ticket->category->name ?? 'N/A' }}</span>
                                </li>
                                <li class="list-group-item px-0 py-2 d-flex justify-content-between">
                                    <span class="font-weight-bold">Reported By:</span>
                                    <span>{{ $ticket->reported_customer }}</span>
                                </li>
                                @if($ticket->assigned_to)
                                    <li class="list-group-item px-0 py-2 d-flex justify-content-between">
                                        <span class="font-weight-bold">Assigned To:</span>
                                        <span>{{ $ticket->assignedTo->name }}</span>
                                    </li>
                                @endif
                                @if($ticket->time_solved)
                                    <li class="list-group-item px-0 py-2 d-flex justify-content-between">
                                        <span class="font-weight-bold">Resolved On:</span>
                                        <span>{{ $ticket->time_solved->format('M d, Y h:i A') }}</span>
                                    </li>
                                @endif
                                @if($ticket->response_time)
                                    <li class="list-group-item px-0 py-2 d-flex justify-content-between">
                                        <span class="font-weight-bold">Response Time:</span>
                                        <span>{{ $ticket->response_time }} minutes</span>
                                    </li>
                                @endif
                                <li class="list-group-item px-0 py-2 d-flex justify-content-between">
                                    <span class="font-weight-bold">Times Reopened:</span>
                                    <span class="badge badge-{{ $ticket->reopen_history_count > 0 ? 'warning' : 'secondary' }}">
                                        {{ $ticket->reopen_history_count }}
                                    </span>
                                </li>
                            </ul>
                        </div>

                        @if(!is_null($ticket->satisfaction) || $ticket->feedback_submitted_at)
                            <div class="mb-4">
                                <h6 class="font-weight-bold text-gray-800 mb-3">Feedback</h6>
                                <ul class="list-group list-group-flush">
                                    @if(!is_null($ticket->satisfaction))
                                        <li class="list-group-item px-0 py-2 d-flex justify-content-between">
                                            <span class="font-weight-bold">Satisfaction:</span>
                                            <span class="badge badge-{{ $ticket->satisfaction ? 'success' : 'danger' }}">
                                                {{ $ticket->satisfaction ? 'Satisfied' : 'Not Satisfied' }}
                                            </span>
                                        </li>
                                    @endif
                                    @if($ticket->feedback_submitted_at)
                                        <li class="list-group-item px-0 py-2 d-flex justify-content-between">
                                            <span class="font-weight-bold">Feedback Submitted:</span>
                                            <span>{{ $ticket->feedback_submitted_at->format('M d, Y h:i A') }}</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Status Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-tasks mr-2"></i>Ticket Status
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(in_array($ticket->status, ['closed']))
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                This ticket is <strong>{{ ucfirst($ticket->status) }}</strong> and cannot be updated.
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold">Current Status:</span>
                            <span class="badge badge-{{ $ticket->status_badge }}">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </div>

                        @if($ticket->updated_at)
                            <div class="mt-2">
                                <small class="text-muted">
                                    Last updated {{ $ticket->updated_at->diffForHumans() }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Custom Styles */
        .ticket-description, .comment-content {
            white-space: pre-wrap;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        #comments-container {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .scrollable-activity-log {
            max-height: 250px;
            overflow-y: auto;
            padding-right: 10px;
        }

        /* Timeline Styles */
        .timeline {
            position: relative;
            padding-left: 20px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 10px;
        }

        .timeline-marker {
            position: absolute;
            left: -20px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #4e73df;
            border: 2px solid white;
        }

        .timeline-content {
            margin-left: 10px;
        }

        /* Badge Colors */
        .badge-open { background-color: #f6c23e; color: #000; }
        .badge-in_progress { background-color: #36b9cc; color: #fff; }
        .badge-resolved { background-color: #1cc88a; color: #fff; }
        .badge-closed { background-color: #e74a3b; color: #fff; }
        .badge-low { background-color: #858796; color: #fff; }
        .badge-medium { background-color: #4e73df; color: #fff; }
        .badge-high { background-color: #f6c23e; color: #000; }
        .badge-critical { background-color: #e74a3b; color: #fff; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Card Header Enhancements */
        .card-header.bg-primary {
            border-radius: 0.35rem 0.35rem 0 0 !important;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .d-sm-flex.justify-content-between {
                flex-direction: column;
            }

            .d-sm-flex.justify-content-between > * {
                margin-bottom: 10px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Delete confirmation
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

        // Comment sorting functionality
        document.getElementById('comment-sort').addEventListener('change', function() {
            const container = document.getElementById('comments-container');
            const comments = Array.from(container.querySelectorAll('.comment-item'));

            comments.reverse(); // Toggle order

            // Remove all comments
            while (container.firstChild) {
                container.removeChild(container.firstChild);
            }

            // Re-add in new order
            comments.forEach(comment => {
                // Remove border from last item
                if (comment === comments[comments.length - 1]) {
                    comment.classList.remove('border-bottom', 'pb-4');
                } else {
                    comment.classList.add('border-bottom', 'pb-4');
                }
                container.appendChild(comment);
            });
        });
    </script>
@endpush
