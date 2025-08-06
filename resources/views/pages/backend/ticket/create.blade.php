@extends('layouts.backend.app')
@section('title', __('label.add_ticket'))

@section('content')
    <div class="container py-5 bg-white">
        <div class="row">
            <div class="col-md-6">
                <form id="ticketForm" novalidate>
                    @csrf

                    <!-- Service Selection -->
                    <div class="mb-3 form-section visible-section" id="serviceSection">
                        <label for="service" class="form-label">{{__('label.saas_app')}} <span class="text-danger">*</span></label>
                        <select class="form-control select2-ajax" id="service" name="service"
                                data-placeholder="Search for a SaaS app..."
                                data-ajax-url="{{ route('backend.saas_app.search') }}" required>
                            <option value=""></option>
                            @if(isset($preSelectedService))
                                <option value="{{ $preSelectedService->id }}" selected>{{ $preSelectedService->name }}</option>
                            @endif
                        </select>
                        <div class="form-text">Start typing to search for a SaaS app</div>
                    </div>

                    <!-- Client Selection -->
                    <div class="mb-3 form-section hidden-section" id="clientSection">
                        <label for="client" class="form-label">{{__('label.client')}} <span class="text-danger">*</span></label>
                        <div id="clientSelectContainer">
                            <select class="form-select form-control select2" id="client" required>
                                <option value="">Select a client</option>
                                @if(isset($preSelectedClient))
                                    <option value="{{ $preSelectedClient->id }}" selected>{{ $preSelectedClient->name }}</option>
                                @endif
                            </select>
                        </div>

                        <div class="mt-2 hidden-section" id="clientNameInputSection">
{{--                            <label for="clientName" class="form-label">Client Full Name <span class="text-danger">*</span></label>--}}
                            <input type="text" class="form-control" id="clientName" placeholder="Enter client's full name">
                            <div class="form-text">Please enter the full name of the client</div>
                        </div>

                        <div class="form-text" id="clientHistoryText"></div>
                    </div>

                    <!-- Topic Selection -->
                    <div class="mb-3 form-section hidden-section" id="topicSection">
                        <label for="topic" class="form-label">Topic <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="topic" required >
                            <option value="" >Select a topic</option>
                        </select>
                    </div>

                    <!-- SMS Specific Fields -->
                    <div class="mb-3 form-section hidden-section" id="dateSection">
                        <label for="issueDate" class="form-label">Issue Date</label>
                        <input type="date" class="form-control" id="issueDate" max="{{ date('Y-m-d') }}">
                        <div class="form-text">Please specify when the issue occurred</div>
                    </div>

                    <!-- SMS Specific Fields -->
                    <div class="mb-3 form-section hidden-section" id="senderIdSection">
                        <label for="senderId" class="form-label">Sender ID <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="senderId" required >
                            <option value="" >Select sender ID</option>
                        </select>
                        <div class="form-text">Select the sender ID for SMS delivery</div>
                    </div>

                    <div class="mb-3 form-section hidden-section" id="operatorSection">
                        <label for="operator" class="form-label">Mobile Operator <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="operator" multiple required>
                            <option value="">Select mobile operator(s)</option>
                        </select>
                        <div class="form-text">Select one or more mobile network operators</div>
                    </div>

                    <!-- Payment Specific Fields -->
                    <div class="mb-3 form-section hidden-section" id="paymentChannelSection">
                        <label for="paymentChannel" class="form-label">Payment Channel <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="paymentChannel" required >
                            <option value="" >Select a payment channel</option>
                        </select>
                        <div class="form-text">Select the payment method related to this issue</div>
                    </div>

                    <!-- Subtopic Selection -->
                    <div class="mb-3 form-section hidden-section" id="subtopicSection">
                        <label for="subtopic" class="form-label">{{__('label.subtopic')}} <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="subtopic" required >
                            <option value="" >Select a subtopic</option>
                        </select>
                    </div>

                    <!-- Tertiary Topic Selection -->
                    <div class="mb-3 form-section hidden-section" id="tertiaryTopicSection">
                        <label for="tertiaryTopic" class="form-label">Tertiary Topic</label>
                        <select class="form-select select2" id="tertiaryTopic">
                            <option value="" >Select a tertiary topic (optional)</option>
                        </select>
                    </div>

                    <!-- Ticket Details -->
                    <div class="mb-3 form-section hidden-section" id="subjectSection">
                        <label for="subject" class="form-label">{{__('label.subject')}} </label>
                        <input type="text" class="form-control" id="subject" required>
                    </div>

                    <div class="mb-3 form-section hidden-section" id="descriptionSection">
                        <label for="description" class="form-label">{{__('label.description')}} </label>
                        <textarea class="form-control" id="description" rows="4" required></textarea>
                    </div>

                    <div class="mb-3 form-section hidden-section" id="prioritySection">
                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="priority" required>
                            <option value="">Select priority</option>
                            @foreach($priorities as $priority)
                                <option value="{{ strtolower($priority->name) }}">{{ $priority->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Manager Selection -->
                    <div class="mb-3 form-section hidden-section" id="managerSection">
                        <label for="manager" class="form-label">Assign To <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="manager" required >
                            <option value="" >Select a manager</option>
                        </select>
                        <div class="form-text">Managers with experience in this topic are shown first</div>
                    </div>

                    <div class="mb-3 form-section hidden-section" id="attachmentsSection">
                        <label for="attachments" class="form-label">Attachments</label>
                        <div class="input-group">
                            <input class="form-control" type="file" id="attachments" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <button class="btn btn-outline-secondary" type="button" id="addMoreFiles">
                                <i class="bi bi-plus-circle"></i> Add More
                            </button>
                        </div>
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> You can select multiple files at once or add more files later (Max 2MB each)
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Files selected: <span id="fileCount">0</span></small>
                        </div>
                        <div id="attachmentPreviews" class="mt-2"></div>
                    </div>

                    <div class="form-section hidden-section" id="submitSection">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span id="submitText">Create Ticket</span>
                            <span id="submitSpinner" class="loading-spinner" style="display: none;"></span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-md-6">
                <div class="mb-3 form-section hidden-section" id="ticketHistorySection">
                    <div class="ticket-history-container">
                        <div class="ticket-history-header">
                            <h4 class="ticket-history-title">
                                Recent Tickets
                            </h4>
                            <span class="ticket-history-count">Loading...</span>
                        </div>
                        <div id="ticketHistory" class="ticket-history-list">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 mb-0">Loading ticket history...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Channel Modal -->
        <div class="modal fade" id="channelModal" tabindex="-1" role="dialog" aria-labelledby="channelModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Select Notification Channels</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row text-center">

                            @foreach($channels as $channel)
                                <div class="col-md-3">
                                    <div class="card channel-card shadow-sm" data-channel="{{ $channel->name }}">
                                        <div class="card-body">
                                            <i class="{{ $channel->icon }} fa-2x text-{{ $channel->color }} mb-2"></i>
                                            <h6>{{ ucfirst($channel->name) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div id="notification_channels_wrapper"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('label.skip')}}</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">{{__('label.confirm')}}</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Preview Modal -->
        <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-labelledby="imagePreviewModal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{__('label.image_preview')}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('label.close')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /**
        notifications channels
         */
        .channel-card.locked {
            cursor: not-allowed;
            opacity: 0.8;
            border-color: #6c757d !important;
            background-color: rgba(108, 117, 125, 0.1) !important;
        }

        .channel-card.locked i {
            color: #6c757d !important;
        }

        .channel-summary {
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 0.25rem;
        }

        .channel-summary .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }

        .click-to-edit:hover {
            text-decoration: underline;
        }

        .form-section {
            transition: all 0.3s ease;
        }

        .hidden-section {
            display: none;
        }
        /**
        ticket history
         */
        .ticket-history-container {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 1rem;
            background: #f8f9fa;
        }

        .ticket-history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .ticket-history-item {
            padding: 0.75rem;
            margin-bottom: 0.75rem;
            background: white;
            border-radius: 0.25rem;
            border: 1px solid #dee2e6;
        }

        .ticket-history-item-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .ticket-history-item-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: #6c757d;
        }

        .file-preview {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 0.25rem;
        }

        .file-preview-thumbnail {
            margin-right: 1rem;
            width: 60px;
            text-align: center;
        }

        .file-preview-info {
            flex-grow: 1;
        }

        .file-name, .file-size {
            display: block;
        }

        .channel-card {
            cursor: pointer;
            margin-bottom: 1rem;
            transition: all 0.2s;
        }

        .channel-card.selected {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
        }

        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('asset/js/script.js') }}"></script>
@endpush
