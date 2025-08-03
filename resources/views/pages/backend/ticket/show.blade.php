@extends('layouts.backend.app')
@section('title', 'Ticket Details - ' . $ticket->ticket_number)
@section('content')

    <ul class="nav nav-tabs mb-3" id="ticketTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details"
               role="tab" aria-controls="details" aria-selected="true">Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="actions-tab" data-toggle="tab" href="#actions"
               role="tab" aria-controls="actions" aria-selected="false">Actions</a>
        </li>
    </ul>

    <div class="tab-content" id="ticketTabContent">
        <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-end mb-4">
                    <div class="d-flex">
                        @if($ticket->status === 'open')
                            <a href="{{ route('backend.ticket.edit', $ticket->uid) }}" class="btn btn-sm btn-primary mr-2" hidden="">
                                <i class="fas fa-edit fa-sm mr-1"></i> Edit
                            </a>
                            <form id="delete-ticket-form-{{ $ticket->uid }}" action="{{ route('backend.ticket.destroy', $ticket->uid) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $ticket->uid }}')">
                                    <i class="fas fa-trash fa-sm mr-1"></i> {{__('label.delete')}}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Ticket Details Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-database mr-2"></i> Ticket Information
                        </h6>
                        <form action="{{ route('backend.ticket.update-status', $ticket->uid) }}" method="POST" class="mb-0">
                            @csrf
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="statusSwitch"
                                       name="status" value="resolved"
                                       {{ $ticket->status == 'resolved' ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                                <label class="custom-control-label" for="statusSwitch">
                                    Mark as Resolved
                                </label>
                            </div>
                        </form>

                        <span class="badge badge-{{ $ticket->status == 'open' ? 'warning' : ($ticket->status == 'resolved' ? 'success' : ($ticket->status == 'in_progress' ? 'info' : ($ticket->status == 'closed' ? 'secondary' : 'danger'))) }}">
            {{ ucfirst($ticket->status) }}
        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h5 class="font-weight-bold">{{ $ticket->title }}</h5>
                                    <p class="text-muted mb-0">Created: {{ $ticket->created_at->format('M d, Y h:i A') }}</p>
                                    <p class="text-muted">Last Updated: {{ $ticket->updated_at->format('M d, Y h:i A') }}</p>
                                </div>

                                <div class="mb-3">
                                    <h6 class="font-weight-bold">{{__('label.description')}}</h6>
                                    <div class="border p-3 bg-light rounded">
                                        {!! nl2br(e($ticket->description)) !!}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6 class="font-weight-bold">{{__('label.topic')}}</h6>
                                    <p>
                                        @if($ticket->topic)
                                            <span class="badge badge-secondary">{{ $ticket->topic->name }}</span>
                                            @if($ticket->subTopic)
                                                <i class="fas fa-chevron-right mx-2 text-muted"></i>
                                                <span class="badge badge-info">{{ $ticket->subTopic->name }}</span>
                                                @if($ticket->tertiaryTopic)
                                                    <i class="fas fa-chevron-right mx-2 text-muted"></i>
                                                    <span class="badge badge-primary">{{ $ticket->tertiaryTopic->name }}</span>
                                                @endif
                                            @endif
                                        @else
                                            <span class="text-muted">Not categorized</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="mb-3">
                                    <h6 class="font-weight-bold">Priority</h6>
                                    <span class="badge badge-{{ $ticket->priority == 'high' ? 'danger' : ($ticket->priority == 'medium' ? 'warning' : 'success') }}">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                                </div>

                                <!-- Feedback Section -->
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

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6 class="font-weight-bold">Client Information</h6>
                                    <div class="border p-3 bg-light rounded">
                                        @if($ticket->client)
                                            <p class="mb-1"><strong>Name:</strong> {{ $ticket->client->name }}</p>
                                            <p class="mb-1"><strong>Email:</strong> {{ $ticket->client->email ?? 'N/A' }}</p>
                                            <p class="mb-0"><strong>SaaS App:</strong> {{ $ticket->saasApp->name }}</p>
                                        @else
                                            <p>No client associated</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6 class="font-weight-bold">Assigned To</h6>
                                    <div class="border p-3 bg-light rounded">
                                        @if($ticket->assignedTo)
                                            <p class="mb-1"><strong>Name:</strong> {{ $ticket->assignedTo->name }}</p>
                                            <p class="mb-1"><strong>Email:</strong> {{ $ticket->assignedTo->email }}</p>
                                            <p class="mb-0"><strong>Role:</strong> {{ ucfirst($ticket->assignedTo->role) }}</p>
                                        @else
                                            <p>Not assigned</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6 class="font-weight-bold">Additional Information</h6>
                                    <div class="border p-3 bg-light rounded">
                                        @if($ticket->payment_channel_id)
                                            <p class="mb-1"><strong>Payment Channel:</strong> {{ $ticket->paymentChannel->name }}</p>
                                            <p class="mb-1"><strong>Date Occurred:</strong> {{ $ticket->issue_date ?? ''}}</p>
                                        @endif
                                        @if($ticket->sender_id)
                                            <p class="mb-1"><strong>Sender ID:</strong> {{ $ticket->sender->sender_id }}</p>
                                            <p class="mb-1"><strong>Date Occurred:</strong> {{ $ticket->issue_date ?? ''}}</p>
                                        @endif
                                        @if($ticket->operators->isNotEmpty())
                                            <p class="mb-1"><strong>Mobile Operator:</strong> {{ $ticket->operators->pluck('name')->join(', ') }}</p>
                                        @endif
                                        @if($ticket->time_solved)
                                            <p class="mb-0"><strong>Time Solved:</strong> {{ $ticket->time_solved->format('M d, Y h:i A') }}</p>
                                        @endif

                                        <!-- Escalation Information -->
                                        @if($ticket->escalated_at)
                                            <hr>
                                            <p class="mb-1"><strong>Escalated:</strong>
                                                {{ $ticket->escalated_at->format('M d, Y h:i A') }}
                                                <small class="text-muted">({{ $ticket->escalated_at->diffForHumans() }})</small>
                                            </p>
                                            @if($ticket->escalation_reason)
                                                <p class="mb-0"><strong>Escalation Reason:</strong></p>
                                                <div class="mt-1 p-2 bg-light rounded">
                                                    {{ $ticket->escalation_reason }}
                                                </div>
                                            @endif
                                        @endif

                                        <!-- Reopen Count -->
                                        <hr>
                                        <p class="mb-0 d-flex justify-content-between">
                                            <strong>Times Reopened:</strong>
                                            <span class="badge badge-{{ $ticket->reopen_history_count > 0 ? 'warning' : 'secondary' }}">
                                        {{ $ticket->reopen_history_count }}
                                    </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attachments Section -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-paperclip mr-2"></i>Attachments ({{ $ticket->attachments->count() ?? 0}})
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($ticket->attachments->count() > 0)
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

                                                        <div class="mt-2 d-flex gap-2">
                                                            {{-- Preview --}}
                                                            @if(in_array($attachment->mime_type, ['image/jpeg', 'image/png']))
                                                                <button type="button"
                                                                        class="btn btn-sm btn-light"
                                                                        data-toggle="tooltip"
                                                                        title="Preview"
                                                                        data-target="#previewModal"
                                                                        data-toggle="modal"
                                                                        data-image="{{ route('backend.attachment.view', $attachment->uid) }}"
                                                                        data-name="{{ $attachment->original_name }}">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                            @else
                                                                <a href="{{ route('backend.attachment.view', $attachment->uid) }}"
                                                                   target="_blank"
                                                                   class="btn btn-sm btn-light"
                                                                   data-toggle="tooltip"
                                                                   title="Preview">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            @endif

                                                            {{-- Download --}}
                                                            <a href="{{ route('backend.attachment.download', $attachment->uid) }}"
                                                               download
                                                               class="btn btn-sm btn-light"
                                                               data-toggle="tooltip"
                                                               title="Download">
                                                                <i class="fas fa-download"></i>
                                                            </a>

                                                            {{-- Share --}}
                                                            <button type="button"
                                                                    class="btn btn-sm btn-light"
                                                                    data-toggle="tooltip"
                                                                    title="Copy Link"
                                                                    onclick="navigator.clipboard.writeText('{{ route('backend.attachment.view', $attachment->uid) }}'); alert('Link copied to clipboard')">
                                                                <i class="fas fa-share-alt"></i>
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No attachments found for this ticket.</p>
                        @endif
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-comments mr-2"></i>Comments
                        </h6>
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addCommentModal">
                            <i class="fas fa-plus mr-1"></i> Add Comment
                        </button>
                    </div>
                    <div class="card-body">
                        @if($ticket->comments->count() > 0)
                            <div class="timeline">
                                @foreach($ticket->comments as $comment)
                                    <div class="timeline-item mb-4">
                                        <div class="timeline-header d-flex justify-content-between mb-2">
                                            <span class="font-weight-bold">{{ $comment->user->name }}</span>
                                            <small class="text-muted">{{ $comment->created_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                        <div class="timeline-body border-left pl-3 pb-3">
                                            <p>{!! nl2br(e($comment->content)) !!}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No comments yet. Be the first to add one!</p>
                        @endif
                    </div>
                </div>

                <!-- Status History Section -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"> <i class="fas fa-history mr-2"></i> Status History</h6>
                    </div>
                    <div class="card-body">
                        @if($ticket->statusHistory->count() > 0)
                            <div class="timeline">
                                @foreach($ticket->statusHistory as $history)
                                    <div class="timeline-item mb-4">
                                        <div class="timeline-header d-flex justify-content-between mb-2">
                                            <span class="font-weight-bold">{{ $history->changedByUser->name ?? 'System' }}</span>
                                            <small class="text-muted">{{ $history->created_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                        <div class="timeline-body border-left pl-3 pb-3">
                                            <p>
                                                Changed status from
                                                <span class="badge badge-{{ $history->from_status == 'open' ? 'warning' : ($history->from_status == 'resolved' ? 'success' : ($history->from_status == 'in_progress' ? 'info' : ($history->from_status == 'closed' ? 'secondary' : 'danger'))) }}">
                                            {{ ucfirst($history->from_status) }}
                                        </span>
                                                to
                                                <span class="badge badge-{{ $history->to_status == 'open' ? 'warning' : ($history->to_status == 'resolved' ? 'success' : ($history->to_status == 'in_progress' ? 'info' : ($history->to_status == 'closed' ? 'secondary' : 'danger'))) }}">
                                            {{ ucfirst($history->to_status) }}
                                        </span>
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No status history available for this ticket.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="actions" role="tabpanel" aria-labelledby="actions-tab">
            @include('pages.backend.ticket.partials.actions_form', ['ticket' => $ticket])
        </div>
    </div>



    <!-- Add Comment Modal -->
    <div class="modal fade" id="addCommentModal" tabindex="-1" role="dialog" aria-labelledby="addCommentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('backend.comment.store', $ticket->uid) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCommentModalLabel">Add New Comment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="commentContent">Comment</label>
                            <textarea class="form-control" id="commentContent" name="content" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Comment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Attachment Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="previewImage" src="" alt="" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .timeline {
            position: relative;
            padding-left: 1rem;
        }
        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 10px;
            width: 2px;
            background: #e9ecef;
        }
        .timeline-item {
            position: relative;
            padding-left: 2rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #4e73df;
            margin-left: 6px;
        }
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

        $(function () {
            $('[data-toggle="tooltip"]').tooltip();

            $('[data-target="#previewModal"]').on('click', function () {
                const imageUrl = $(this).data('image');
                const imageName = $(this).data('name');

                $('#previewImage').attr('src', imageUrl).attr('alt', imageName);
                $('#previewModal').modal('show');
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            const select = document.getElementById("assigned_to");

            fetch('/backend/user/active_manager')
                .then(response => response.json())
                .then(data => {
                    if (data.data && data.data.length > 0) {
                        let userData = data.data;
                        userData.forEach(user => {
                            const option = document.createElement("option");
                            option.value = user.id;
                            option.textContent = user.name;

                            @if($ticket->assigned_to)
                                if (parseInt(user.id) === {{ $ticket->assigned_to }}) {
                                    option.selected = true;
                                }
                            @endif

                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                   // console.error("Error fetching users:", error);
                });
        });

        $(document).ready(function() {
            // Handle status update form submission
            $('#updateStatusForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const progressBar = $('#statusProgressBar');
                const progressBarInner = progressBar.find('.progress-bar');
                const submitBtn = $('#statusSubmitBtn');

                // Show progress bar
                progressBar.removeClass('d-none');
                submitBtn.prop('disabled', true);

                // Simulate progress
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 5;
                    progressBarInner.css('width', progress + '%');
                    if (progress >= 100) clearInterval(progressInterval);
                }, 200);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        clearInterval(progressInterval);
                        progressBarInner.css('width', '100%');

                        toastr.success(response.message || 'Status updated successfully');

                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        clearInterval(progressInterval);
                        progressBar.addClass('d-none');
                        submitBtn.prop('disabled', false);
                        toastr.error(xhr.responseJSON.message || 'Error updating status');
                    }
                });
            });

            // Handle reassign form submission
            $('#reassignForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const progressBar = $('#reassignProgressBar');
                const progressBarInner = progressBar.find('.progress-bar');
                const submitBtn = $('#reassignSubmitBtn');

                // Show progress bar
                progressBar.removeClass('d-none');
                submitBtn.prop('disabled', true);

                // Simulate progress
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 5;
                    progressBarInner.css('width', progress + '%');
                    if (progress >= 100) clearInterval(progressInterval);
                }, 200);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        clearInterval(progressInterval);
                        progressBarInner.css('width', '100%');

                        toastr.success(response.message || 'Ticket reassigned successfully');

                        // Reload page after delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        clearInterval(progressInterval);
                        progressBar.addClass('d-none');
                        submitBtn.prop('disabled', false);
                        toastr.error(xhr.responseJSON.message || 'Error reassigning ticket');
                    }
                });
            });
        });
    </script>
@endpush
