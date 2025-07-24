@extends('layouts.backend.app')
@section('title', __('label.client_details'))

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="h6 mb-0 font-weight-bold text-gray-800">
                        <i class="fas fa-bookmark mr-2 text-primary"></i>
                        {{__('label.client_details')}}
                    </h5>
                </div>
                <div>
                    <a href="{{ route('backend.client.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> {{__('label.back_to_list')}}
                    </a>
                    <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#updatePasswordModal">
                        <i class="fas fa-key mr-1"></i> {{__('label.update_pass')}}
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-lg-8">
                        <!-- Basic Information Section -->
                        <div class="detail-card mb-4">
                            <div class="detail-header">
                                <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i> Basic Information</h6>
                            </div>
                            <div class="detail-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">{{__('label.name')}}</label>
                                            <p class="detail-value">{{ $client->name }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">{{__('label.status')}}</label>
                                            <p class="detail-value">
                                                <span class="badge badge-pill {{ $client->is_active ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $client->is_active ? __('label.active') : __('label.inactive') }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row my-4">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">{{__('label.email')}}</label>
                                            <p class="detail-value">{{ $client->email }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">{{__('label.phone')}}</label>
                                            <p class="detail-value">{{ $client->phone ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sender IDs Section with Modal -->
                        <div class="detail-card mb-4">
                            <div class="detail-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-id-card mr-2"></i> Assigned Sender IDs ({{ $client->senderIds->count() }})</h6>
                                <div>
                                    <button class="btn btn-sm btn-outline-primary mr-2" data-toggle="modal" data-target="#assignSenderIdModal">
                                        <i class="fas fa-plus mr-1"></i> Assign
                                    </button>
                                    <a href="{{ route('backend.sender_id.index', ['client_id' => $client->uid]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-list mr-1"></i> View All
                                    </a>
                                </div>
                            </div>
                            <div class="detail-body">
                                @if($client->senderIds->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead>
                                            <tr>
                                                <th>Sender ID</th>
                                                <th>Status</th>
                                                <th>Created At</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($client->senderIds->take(5) as $senderId)
                                                <tr>
                                                    <td>{{ $senderId->sender_id }}</td>
                                                    <td>
                                                            <span class="badge badge-{{ $senderId->status === 'active' ? 'success' : 'warning' }}">
                                                                {{ ucfirst($senderId->status) }}
                                                            </span>
                                                    </td>
                                                    <td>{{ $senderId->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        <a href="#" class="text-danger detach-senderid"
                                                           data-senderid="{{ $senderId->id }}"
                                                           data-client="{{ $client->id }}"
                                                           data-toggle="modal"
                                                           data-target="#confirmDetachModal">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">No sender IDs assigned yet.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Tickets Section -->
                        <div class="detail-card mb-4">
                            <div class="detail-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-ticket-alt mr-2"></i> Latest Tickets</h6>
                                <div>
                                    <a href="{{ route('backend.ticket.create', ['client_id' => $client->uid]) }}" class="btn btn-sm btn-outline-primary mr-2">
                                        <i class="fas fa-plus mr-1"></i> Create New
                                    </a>
                                    <a href="{{ route('backend.ticket.index', ['client_id' => $client->uid]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-list mr-1"></i> View All
                                    </a>
                                </div>
                            </div>
                            <div class="detail-body">
                                @if($client->tickets->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead>
                                            <tr>
                                                <th>Ticket #</th>
                                                <th>Subject</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($client->tickets()->latest()->take(5)->get() as $ticket)
                                                <tr>
                                                    <td>#{{ $ticket->ticket_number }}</td>
                                                    <td>
                                                        <a href="{{ route('backend.ticket.show', $ticket->uid) }}">
                                                            {{ Str::limit($ticket->title, 30) }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                            <span class="badge badge-{{ $ticket->status === 'closed' ? 'secondary' : ($ticket->status === 'open' ? 'success' : 'warning') }}">
                                                                {{ ucfirst($ticket->status) }}
                                                            </span>
                                                    </td>
                                                    <td>{{ $ticket->created_at->diffForHumans() }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">No tickets created yet.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Activity Logs Section -->
                        <div class="detail-card">
                            <div class="detail-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-history mr-2"></i> Recent Activity</h6>
                                <a href="{{ route('backend.logs.index', ['subject_id' => $client->id, 'subject_type' => get_class($client)]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-list mr-1"></i> View All
                                </a>
                            </div>
                            <div class="detail-body">
                                @if($client->activities->count() > 0)
                                    <div class="activity-timeline">
                                        @foreach($client->activities as $activity)
                                            <div class="activity-item mb-3">
                                                <div class="d-flex">
                                                    <div class="activity-icon mr-3">
                                                        <i class="fas fa-{{ $activity->getIcon() }} text-{{ $activity->getColor() }}"></i>
                                                    </div>
                                                    <div class="activity-content">
                                                        <p class="mb-1">{{ $activity->description }}</p>
                                                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted mb-0">No recent activity found.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Meta Information Sidebar -->
                    <div class="col-lg-4">
                        <div class="detail-card">
                            <div class="detail-header">
                                <h6 class="mb-0"><i class="fas fa-history mr-2"></i> System Information</h6>
                            </div>
                            <div class="detail-body">
                                <div class="detail-item">
                                    <label class="detail-label">{{__('label.created_at')}}</label>
                                    <p class="detail-value">
                                        {{ $client->created_at->format('M d, Y') }}
                                        <small class="text-muted d-block">{{ $client->created_at->format('h:i A') }}</small>
                                    </p>
                                </div>
                                <div class="detail-item">
                                    <label class="detail-label">{{__('label.updated_at')}}</label>
                                    <p class="detail-value">
                                        {{ $client->updated_at->format('M d, Y') }}
                                        <small class="text-muted d-block">{{ $client->updated_at->format('h:i A') }}</small>
                                    </p>
                                </div>
                                <div class="detail-item">
                                    <label class="detail-label">Last Activity</label>
                                    <p class="detail-value">
                                        {{ $client->updated_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end border-top pt-3">
                    <a href="{{ route('backend.client.edit', $client->uid) }}" class="btn btn-primary mr-2">
                        <i class="fas fa-edit mr-1"></i> {{__('label.edit')}}
                    </a>
                    <a href="{{ route('backend.client.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list mr-1"></i> {{__('label.view_all')}}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Password Modal -->
    <div class="modal fade" id="updatePasswordModal" tabindex="-1" role="dialog" aria-labelledby="updatePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updatePasswordModalLabel">Update Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('backend.client.update_password', $client->uid) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assign Sender ID Modal -->
    <div class="modal fade" id="assignSenderIdModal" tabindex="-1" role="dialog" aria-labelledby="assignSenderIdModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignSenderIdModalLabel">Assign Sender ID</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('backend.client.assign_sender_id', $client->uid) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="sender_id">Select Sender ID</label>
                            <select class="form-control" id="sender_id" name="sender_id" required>
                                <option value="">-- Select Sender ID --</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Detach Sender ID Modal -->
    <div class="modal fade" id="confirmDetachModal" tabindex="-1" role="dialog" aria-labelledby="confirmDetachModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDetachModalLabel">Confirm Detach</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="detachSenderIdForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Are you sure you want to detach this Sender ID from the client?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Detach</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Handle detach sender ID confirmation
                $('.detach-senderid').on('click', function() {
                    var senderId = $(this).data('senderid');
                    var clientId = $(this).data('client');
                    var actionUrl = '/admin/clients/' + clientId + '/sender-ids/' + senderId + '/detach';

                    $('#detachSenderIdForm').attr('action', actionUrl);
                });
            });
        </script>
    @endpush

    <style>
        .detail-card {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            margin-bottom: 1.5rem;
        }
        .detail-header {
            background-color: #f8f9fc;
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #e3e6f0;
        }
        .detail-body {
            padding: 1.25rem;
        }
        .detail-item {
            margin-bottom: 1rem;
        }
        .detail-item:last-child {
            margin-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }
        .detail-value {
            font-size: 1rem;
            color: #343a40;
            margin-bottom: 0;
        }
        .activity-icon {
            font-size: 1.2rem;
            margin-top: 2px;
        }
        .activity-content {
            flex: 1;
        }
        .activity-item {
            border-left: 2px solid #dee2e6;
            padding-left: 15px;
            position: relative;
        }
        .activity-item:before {
            content: '';
            position: absolute;
            left: -6px;
            top: 8px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #6c757d;
        }
    </style>
@endsection
