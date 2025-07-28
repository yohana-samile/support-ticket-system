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
                                <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i> {{__('label.basic_information')}}</h6>
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
                                        <i class="fas fa-plus mr-1"></i> {{__('label.assign')}}
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
                                                    <th>{{__('label.sender')}}</th>
                                                    <th>{{__('label.status')}}</th>
                                                    <th>{{__('label.assigned_at')}}</th>
                                                    <th>{{__('label.actions')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($client->senderIds->take(5) as $senderId)
                                                    <tr>
                                                        <td>{{ $senderId->sender_id }}</td>
                                                        <td>
                                                            <span class="badge badge-success">
                                                                {{ ('assigned') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $senderId->created_at->format('M d, Y') }}</td>
                                                        <td>
                                                            <form id="detach-sender-id-form-{{ $senderId->id }}"
                                                                  method="POST"
                                                                  action="{{ route('backend.client.detach_sender_id', ['client' => $client->id, 'senderId' => $senderId->id]) }}">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="button"
                                                                        class="btn btn-link text-danger p-0"
                                                                        onclick="confirmRemoveSenderId({{ $senderId->id }})"
                                                                        title="Remove Sender ID">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </form>
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
                                    <a href="{{ route('backend.ticket.create', ['saas_app_id' => $client->saas_app_id, 'client_id' => $client->id]) }}" class="btn btn-sm btn-outline-primary mr-2">
                                        <i class="fas fa-plus mr-1"></i> {{__('label.create')}}
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
                                                    <th>{{__('label.status')}}</th>
                                                    <th>{{__('label.created')}}</th>
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
                                <h6 class="mb-0"><i class="fas fa-history mr-2"></i> {{__('label.system_information')}}</h6>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('label.cancel')}}</button>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assign Sender ID Modal -->
    <div class="modal fade" id="assignSenderIdModal" tabindex="-1" role="dialog" aria-labelledby="assignSenderIdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignSenderIdModalLabel">Assign Sender IDs</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('backend.client.assign_sender_id', $client->uid) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="sender_id">{{__('label.select_sender')}}</label>
                            <select class="form-control select2-multiple" id="sender_id" name="sender_id[]" multiple="multiple" data-placeholder="Search and select sender IDs..." data-ajax-url="{{ route('backend.sender_id.search') }}">
                                <option value=""></option>
                                @if(old('sender_id'))
                                    @foreach(old('sender_id') as $id)
                                        <option value="{{ $id }}" selected>{{ $id }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('sender_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('label.cancel')}}</button>
                        <button type="submit" class="btn btn-primary">{{__('label.assign')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2-multiple').select2({
                ajax: {
                    url: $('#sender_id').data('ajax-url'),
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
                                id: item.id,
                                text: item.sender_id
                            })),
                            pagination: {
                                more: data.next_page_url
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
                placeholder: $('#sender_id').data('placeholder'),
                allowClear: true,
                multiple: true,
                width: '100%',
                dropdownAutoWidth: true,
                escapeMarkup: function(markup) { return markup; },
                templateResult: formatSenderId,
                templateSelection: formatSenderIdSelection
            });

            // Format how results are displayed
            function formatSenderId(sender) {
                if (sender.loading) {
                    return sender.text;
                }

                var $container = $(
                    '<div class="d-flex justify-content-between">' +
                    '<span>' + sender.text + '</span>' +
                    '</div>'
                );

                return $container;
            }

            // Format how selected items are displayed
            function formatSenderIdSelection(sender) {
                return sender.text.split(' (')[0];
            }

            // Clear select2 when modal is closed
            $('#assignSenderIdModal').on('hidden.bs.modal', function () {
                $('#sender_ids').val(null).trigger('change');
            });
        });


        function confirmRemoveSenderId(senderId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will remove the sender ID from this client!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const formId = `detach-sender-id-form-${senderId}`;
                    const form = document.getElementById(formId);

                    if (form) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                                form.submit();
                            }
                        });
                    } else {
                        console.error(`Form not found: ${formId}`);
                        Swal.fire('Error', 'Failed to remove sender ID', 'error');
                    }
                }
            });
        }
    </script>
@endpush
