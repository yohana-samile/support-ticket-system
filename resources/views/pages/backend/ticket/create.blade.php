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
    <script>
        $(document).ready(function () {
            // Constants and Configuration
            const config = {
                MAX_FILE_SIZE: 2 * 1024 * 1024, // 2MB
                ALLOWED_FILE_TYPES: [
                    'image/jpeg',
                    'image/png',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ]
            };

            // State Management
            const state = {
                currentFiles: [],
                selectedChannels: [],
                isCriticalPriority: false,
                preSelectedServiceId: new URLSearchParams(window.location.search).get('saas_app_id'),
                preSelectedClientId: new URLSearchParams(window.location.search).get('client_id'),
                isWorkingCustomer: false
            };

            // DOM Elements
            const elements = {
                service: $('#service'),
                client: $('#client'),
                clientName: $('#clientName'),
                topic: $('#topic'),
                subtopic: $('#subtopic'),
                tertiaryTopic: $('#tertiaryTopic'),
                priority: $('#priority'),
                attachments: document.getElementById('attachments'),
                addMoreBtn: document.getElementById('addMoreFiles'),
                fileCountDisplay: document.getElementById('fileCount'),
                attachmentPreviews: document.getElementById('attachmentPreviews'),
                ticketHistory: document.getElementById('ticketHistory')
            };

            // Initialize the application
            initialize();

            if (state.preSelectedServiceId) {
                elements.service.val(state.preSelectedServiceId).trigger('change');
            }

            // Core Functions
            function initialize() {
                initializeSelect2();
                elements.service.off('change.ticket').on('change.ticket', handleServiceChange);
                elements.client.off('change.ticket').on('change.ticket', handleClientChange);
                elements.topic.off('change.ticket').on('change.ticket', handleTopicChange);
                elements.subtopic.off('change.ticket').on('change.ticket', handleSubtopicChange);
                elements.priority.off('change.ticket').on('change.ticket', handlePriorityChange);

                initializeFileUpload();
                initializeNotificationChannels();
                bindFormSubmit();
                $('#clientName').on('input', handleClientNameInput);

                if (state.preSelectedServiceId) {
                    // Load without triggering change
                    fetchPreselectedService(state.preSelectedServiceId);
                }
            }

            function bindEventHandlers() {
                elements.service.on('change', handleServiceChange);
                elements.client.on('change', handleClientChange);
                elements.topic.on('change', handleTopicChange);
                elements.subtopic.on('change', handleSubtopicChange);
                elements.priority.on('change', handlePriorityChange);
            }

            // Select2 Initialization
            function initializeSelect2() {
                $('.select2').select2({ width: '100%' });

                $('.select2-ajax').select2({
                    width: '100%',
                    ajax: {
                        url: elements.service.data('ajax-url'),
                        dataType: 'json',
                        delay: 250,
                        data: params => ({ search: params.term, page: params.page }),
                        processResults: (data) => ({
                            results: data.data,
                            pagination: { more: data.next_page_url !== null }
                        }),
                        cache: true
                    },
                    minimumInputLength: 1,
                    templateResult: d => d.loading ? d.text : $('<div>').text(d.name),
                    templateSelection: d => d.name || d.text
                });
            }

            // Service Related Functions
            function loadServices(preSelectedServiceId = null) {
                showLoading(elements.service[0]);
                elements.service.empty().append('<option value=""></option>');

                elements.service.select2({
                    ajax: {
                        url: elements.service.data('ajax-url'),
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term,
                                page: params.page || 1,
                                id: preSelectedServiceId
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            hideLoading(elements.service[0]);
                            return {
                                results: data.data.map(item => ({
                                    id: item.id,
                                    text: item.name
                                })),
                                pagination: {
                                    more: data.next_page_url ? true : false
                                }
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 2,
                    placeholder: elements.service.data('placeholder'),
                    allowClear: true,
                    escapeMarkup: markup => markup
                }).on('select2:select', handleServiceChange);

                if (preSelectedServiceId) {
                    fetchPreselectedService(preSelectedServiceId);
                } else {
                    setTimeout(() => hideLoading(elements.service[0]), 500);
                }
            }

            function fetchPreselectedService(serviceId) {
                if (elements.service.find(`option[value="${serviceId}"]`).length) {
                    elements.service.val(serviceId).trigger('change');
                    hideLoading(elements.service[0]);
                    return;
                }

                $.ajax({
                    url: elements.service.data('ajax-url'),
                    data: { search: '', id: serviceId, specific: true },
                    dataType: 'json'
                }).done(function(data) {
                    if (data.data?.length) {
                        const service = data.data.find(item => item.id == serviceId);
                        if (service) {
                            const option = new Option(service.name, service.id, true, true);
                            elements.service.append(option).trigger('change');
                        }
                    }
                    hideLoading(elements.service[0]);
                }).fail(() => hideLoading(elements.service[0]));
            }

            function handleServiceChange() {
                const serviceId = $(this).val();
                const serviceName = $(this).find('option:selected').text().toLowerCase();

                state.isWorkingCustomer = serviceName.includes('working customer');

                if (serviceId) {
                    loadClients(serviceId);
                    showSection('#clientSection');
                    clearError('#serviceSection');

                    // Toggle between client select and client name input
                    if (state.isWorkingCustomer) {
                        $('#client').next('.select2-container').hide();
                        $('#client').addClass('hidden-section').prop('required', false);
                        $('#clientNameInputSection').removeClass('hidden-section');
                        hideSections(['#topicSection', '#ticketHistorySection']);
                    } else {
                        $('#client').next('.select2-container').show();
                        $('#client').removeClass('hidden-section').prop('required', true);
                        $('#clientNameInputSection').addClass('hidden-section');
                        $('#clientName').val('');
                    }
                } else {
                    hideSections(['#clientSection', '#topicSection']);
                }
            }

            function handleClientNameInput() {
                const clientName = $(this).val().trim();
                if (state.isWorkingCustomer && clientName.length > 0) {
                    // Only load topics if we haven't already shown the topic section
                    if ($('#topicSection').hasClass('hidden-section')) {
                        loadTopics(state.preSelectedServiceId);
                        clearError('#clientSection');
                    }
                } else if (state.isWorkingCustomer) {
                    // Hide topic section if client name is empty
                    hideSections(['#topicSection', '#ticketHistorySection']);
                }
            }

            // Client Related Functions
            function loadClients(serviceId) {
                $.get(`/backend/client/client_by_services/${serviceId}`)
                    .done(({ data }) => {
                        const $client = elements.client.empty().append('<option value="">Select a client</option>');

                        data.forEach(client => {
                            const selected = state.preSelectedClientId == client.id ? 'selected' : '';
                            $client.append(`<option value="${client.id}" ${selected}>${client.name}</option>`);
                        });

                        if (state.preSelectedClientId) {
                            setTimeout(() => elements.client.trigger('change'), 100);
                        }
                    })
                    .fail(() => elements.client.html('<option>Error loading clients</option>'));
            }

            function handleClientChange() {
                if (state.isWorkingCustomer) {
                    const clientName = $('#clientName').val().trim();
                    if (!clientName) {
                        showAlert('error', 'Please enter the client full name for Working Customer');
                        return;
                    }
                    // Proceed with the unregistered client flow
                    loadClientTicketHistory(null);
                    loadTopics(state.preSelectedServiceId);
                    clearError('#clientSection');
                }
                else {
                    const clientId = $('#client').val();
                    if (clientId) {
                        loadClientTicketHistory(clientId);
                        loadTopics(state.preSelectedServiceId);
                        clearError('#clientSection');
                    } else {
                        hideSections(['#topicSection', '#ticketHistorySection']);
                        // Reset Select2 if needed
                        $('#client').val('').trigger('change');
                    }
                }
            }

            // Ticket History Functions
            async function loadClientTicketHistory(clientId) {
                try {
                    const response = await fetch(`/backend/ticket/client_ticket_history/${clientId}`);
                    const responseData = await response.json();
                    const tickets = responseData?.data?.data;

                    if (!Array.isArray(tickets)) {
                        throw new Error('Invalid data format received from server');
                    }

                    showSection('#ticketHistorySection');
                    renderTicketHistory(tickets);
                } catch (error) {
                    renderTicketHistoryError(error.message);
                }
            }

            function renderTicketHistory(tickets) {
                if (!elements.ticketHistory) return;

                if (tickets.length === 0) {
                    elements.ticketHistory.innerHTML = `
                        <div class="empty-state text-center py-5">
                            <i class="bi bi-inbox-fill empty-state-icon fs-1 text-muted"></i>
                            <h4 class="empty-state-title mt-3">No Previous Tickets</h4>
                            <p class="empty-state-text text-muted">This client hasn't submitted any tickets yet.</p>
                        </div>`;
                    return;
                }

                const clientName = tickets[0]?.client?.name || 'Client';
                const ticketsToShow = tickets.slice(0, 5);

                elements.ticketHistory.innerHTML = `
                    <div class="ticket-history-header d-flex justify-content-between align-items-center mb-3">
                        <h4 class="ticket-history-title mb-0 d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary-emphasis client-badge">
                                <i class="bi bi-person-circle me-1"></i> ${clientName}
                            </span>
                        </h4>
                        <span class="ticket-history-count badge bg-light text-dark">
                            ${Math.min(tickets.length, 5)}/${tickets.length}
                        </span>
                    </div>
                    <div id="ticketHistoryList" class="ticket-list"></div>
                `;

                const list = document.getElementById('ticketHistoryList');
                ticketsToShow.forEach(ticket => {
                    list.appendChild(createTicketItem(ticket));
                });

                if (tickets.length > 5) {
                    addViewAllButton(tickets[0]?.client?.uid);
                }
            }

            function renderTicketHistoryError(errorMessage) {
                if (elements.ticketHistory) {
                    elements.ticketHistory.innerHTML = `
                        <div class="alert alert-danger m-3">
                            <i class="bi bi-exclamation-triangle-fill"></i> Failed to load ticket history: ${errorMessage}
                        </div>`;
                }
                hideSection('#ticketHistorySection');
            }

            function createTicketItem(ticket) {
                const createdDate = ticket.created_at ? new Date(ticket.created_at) : new Date();
                const isResolved = ticket.status?.toLowerCase() === 'resolved';
                const categoryPath = buildCategoryPath(ticket);

                const item = document.createElement('div');
                item.className = `ticket-history-item mb-3 p-3 rounded-3 border ${isResolved ? 'bg-light' : 'bg-white'} shadow-sm`;
                item.style.transition = 'all 0.2s ease';
                item.style.cursor = 'pointer';

                item.onmouseenter = () => item.style.transform = 'translateY(-2px)';
                item.onmouseleave = () => item.style.transform = '';

                item.innerHTML = `
                    <div class="ticket-history-item-header d-flex justify-content-between align-items-center mb-2">
                        <span class="ticket-number fw-semibold text-primary">
                            <i class="bi bi-ticket-detailed me-1"></i> ${ticket.ticket_number}
                        </span>
                        <span class="ticket-status badge ${getStatusColor(ticket.status)}">
                            ${ticket.status}
                        </span>
                    </div>

                    <h5 class="ticket-title mb-2 text-truncate" title="${ticket.title || 'No subject'}">
                        ${ticket.title || 'No subject provided'}
                    </h5>

                    <div class="ticket-meta d-flex flex-wrap gap-2 mb-3 text-muted small">
                        <span class="ticket-category d-flex align-items-center">
                            <i class="bi bi-folder me-1"></i> ${categoryPath || 'Uncategorized'}
                        </span>
                    </div>
                    <p class="d-flex align-items-center gap-3 flex-wrap mb-0">
                        <span class="ticket-assignee d-inline-flex align-items-center gap-2">
                            <i class="bi bi-person"></i>
                            ${ticket.assigned_to?.name || 'Unassigned'}
                        </span>
                        <span class="ticket-date d-inline-flex align-items-center gap-2">
                            <i class="bi bi-calendar"></i>
                            ${formatTicketDate(ticket.created_at)}
                        </span>
                    </p>

                    <div class="ticket-description mb-3 text-muted">
                        ${ticket.description ? truncateText(ticket.description, 100) : 'No description provided'}
                    </div>

                    <div class="ticket-footer d-flex justify-content-between align-items-center text-white">
                        <span class="ticket-priority badge ${getPriorityBadgeClass(ticket.priority)}">
                            <i class="bi bi-exclamation-circle me-1"></i> ${ticket.priority}
                        </span>
                        <span class="ticket-time text-white badge ${getTimeBadgeClass(ticket.created_at)}">
                            <i class="bi bi-clock me-1"></i> ${timeAgo(createdDate)}
                        </span>
                    </div>
                `;

                item.addEventListener('click', () => {
                    window.location.href = `/backend/ticket/view/${ticket.uid}`;
                });

                return item;
            }

            function buildCategoryPath(ticket) {
                let path = ticket.topic?.name || '';
                if (ticket.subtopic?.name) path += ` › ${ticket.subtopic.name}`;
                if (ticket.tertiary_topic?.name) path += ` › ${ticket.tertiary_topic.name}`;
                return path;
            }

            function addViewAllButton(clientUid) {
                if (!clientUid || !elements.ticketHistory) return;

                const viewAll = document.createElement('a');
                viewAll.href = `/backend/ticket/client_ticket_history/${clientUid}`;
                viewAll.target = '_blank';
                viewAll.className = 'btn btn-outline-primary w-100 mt-3 d-flex align-items-center justify-content-center gap-2';
                viewAll.innerHTML = 'View All Tickets <i class="bi bi-arrow-right"></i>';
                elements.ticketHistory.appendChild(viewAll);
            }

            // Topic Related Functions
            function handleTopicChange() {
                const topicId = $(this).val();
                if (!topicId) return hideSections(['#subtopicSection', '#dateSection']);

                loadSubtopics(topicId);
                const topicName = $(this).find('option:selected').text().toLowerCase();

                if (topicName.includes('sms')) {
                    loadSenderIds(elements.client.val());
                    loadOperators();
                    showSections(['#senderIdSection', '#operatorSection', '#dateSection']);
                    hideSection('#paymentChannelSection');
                    updateDateLabels('SMS Issue Date', 'Please specify when the SMS issue occurred');
                } else if (topicName.includes('payment')) {
                    loadPaymentChannels();
                    showSections(['#paymentChannelSection', '#dateSection']);
                    hideSections(['#senderIdSection', '#operatorSection']);
                    updateDateLabels('Payment Date', 'Please specify when the payment was made');
                } else {
                    hideSections(['#senderIdSection', '#operatorSection', '#dateSection', '#paymentChannelSection']);
                }

                clearError('#topicSection');
            }

            function loadTopics(serviceId) {
                $.get(`/backend/topic/get_by_service/${serviceId}`)
                    .done(({ data }) => {
                        elements.topic.empty().append('<option value="">Select a topic</option>');
                        data.forEach(t => elements.topic.append(`<option value="${t.id}">${t.name}</option>`));
                        showSection('#topicSection');
                    })
                    .fail(() => showAlert('error', 'Failed to load topics'));
            }

            // Subtopic Related Functions
            function handleSubtopicChange() {
                const subtopicId = $(this).val();
                if (!subtopicId) return hideSections(['#subjectSection']);

                loadTertiaryTopics(subtopicId);
                elements.priority.val('low').trigger('change');
                showAlert("info", 'Priority automatically set to "low"');
                showSections(['#subjectSection', '#descriptionSection', '#prioritySection', '#attachmentsSection', '#submitSection']);
                loadManagers();
                clearError('#subtopicSection');
            }

            function loadSubtopics(topicId) {
                $.get(`/backend/subtopic/get_by_topic_id/${topicId}`)
                    .done(({ data }) => {
                        elements.subtopic.empty().append('<option value="">Select a subtopic</option>');
                        data.forEach(st => elements.subtopic.append(`<option value="${st.id}">${st.name}</option>`));
                        showSection('#subtopicSection');
                    })
                    .fail(() => showAlert('error', 'Failed to load subtopics'));
            }

            function loadTertiaryTopics(subtopicId) {
                $.get(`/backend/tertiary/tertiary_topic_by_subtopic_id/${subtopicId}`)
                    .done(({ data }) => {
                        elements.tertiaryTopic.empty().append('<option value="">Select a tertiary topic (optional)</option>');
                        data.forEach(t => elements.tertiaryTopic.append(`<option value="${t.id}">${t.name}</option>`));
                        showSection('#tertiaryTopicSection');
                    })
                    .fail(() => showAlert('error', 'Failed to load tertiary topics'));
            }

            function loadSenderIds(clientId) {
                $.get(`/backend/sender_id/active_sender_ids/${clientId}`)
                    .done(({ data }) => {
                        const $sel = $('#senderId').empty().append('<option value="">Select sender ID</option>');
                        data.forEach(s => $sel.append(`<option value="${s.id}">${s.sender_id}</option>`));
                    });
            }

            function loadOperators() {
                $.get('/backend/operator/get_all_operator')
                    .done(({ data }) => {
                        const $op = $('#operator').empty().append('<option value="">Select mobile operator(s)</option>');
                        data.forEach(o => $op.append(`<option value="${o.id}">${o.name}</option>`));
                    });
            }

            function loadPaymentChannels() {
                $.get('/backend/payment_channel/active_payment_channels')
                    .done(({ data }) => {
                        const $ch = $('#paymentChannel').empty().append('<option value="">Select a payment channel</option>');
                        data.forEach(c => $ch.append(`<option value="${c.id}">${c.name}</option>`));
                    });
            }

            //load staffs users
            function loadManagers() {
                const $manager = $('#manager');
                $manager.empty().append('<option value="">Loading managers...</option>');

                showSection('#managerSection');

                $.get('/backend/user/active_manager')
                    .done(({ data }) => {
                        $manager.empty().append('<option value="">Select a manager</option>');

                        if (data.length > 0) {
                            // Sort managers by favorite count (most experienced first)
                            const sortedManagers = data.sort((a, b) => (b.favorite_count || 0) - (a.favorite_count || 0));

                            sortedManagers.forEach(manager => {
                                $manager.append(
                                    `<option value="${manager.id}">
                                        ${manager.name}
                                        ${manager.favorite_count > 0 ? `(${manager.favorite_count} similar tickets)` : ''}
                                    </option>`
                                );
                            });
                        } else {
                            $manager.append('<option value="">No managers available</option>');
                        }
                    })
                    .fail(() => {
                        $manager.empty().append('<option value="">Error loading managers</option>');
                        showAlert('error', 'Failed to load managers');
                    });
            }

            // File Upload Functions
            function initializeFileUpload() {
                elements.attachments.addEventListener('change', handleFileSelection);
                elements.addMoreBtn.addEventListener('click', handleAddMoreFiles);
            }

            function handleFileSelection(event) {
                const newFiles = Array.from(event.target.files);
                const validationResults = validateFiles(newFiles);

                if (validationResults.invalidFiles.length > 0) {
                    showInvalidFilesAlert(validationResults.invalidFiles);
                }

                const uniqueValidFiles = validationResults.validFiles.filter(newFile =>
                    !state.currentFiles.some(
                        file => file.name === newFile.name &&
                            file.size === newFile.size &&
                            file.lastModified === newFile.lastModified
                    )
                );

                if (uniqueValidFiles.length > 0) {
                    state.currentFiles = [...state.currentFiles, ...uniqueValidFiles];
                    updateFileInput();
                    updateFileDisplay();
                    showAlert('success', `Added ${uniqueValidFiles.length} file(s)`);
                }
            }

            function validateFiles(files) {
                const result = {
                    validFiles: [],
                    invalidFiles: []
                };

                files.forEach(file => {
                    const validation = {
                        file,
                        errors: []
                    };

                    // Size validation
                    if (file.size > config.MAX_FILE_SIZE) {
                        validation.errors.push(`Size exceeds ${formatFileSize(config.MAX_FILE_SIZE)} limit`);
                    }

                    // Type validation
                    if (!config.ALLOWED_FILE_TYPES.includes(file.type)) {
                        const allowedTypes = config.ALLOWED_FILE_TYPES.map(t => t.split('/')[1]).join(', ');
                        validation.errors.push(`Type not allowed (allowed: ${allowedTypes})`);
                    }

                    // Virus scan simulation (would be async in real implementation)
                    if (file.name.toLowerCase().includes('virus')) {
                        validation.errors.push('File appears malicious');
                    }

                    if (validation.errors.length === 0) {
                        result.validFiles.push(file);
                    } else {
                        result.invalidFiles.push(validation);
                    }
                });

                return result;
            }

            function showInvalidFilesAlert(invalidFiles) {
                // Combine all error messages into a single string
                const errorMessages = invalidFiles.map(file => {
                    return `${file.file.name}: ${file.errors.join(', ')}`;
                }).join('\n');

                // Show as a single alert
                showAlert('error', `failed validation:\n${errorMessages}`);
            }

            function handleAddMoreFiles() {
                const tempInput = document.createElement('input');
                tempInput.type = 'file';
                tempInput.multiple = true;
                tempInput.accept = config.ALLOWED_FILE_TYPES.join(',');

                tempInput.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        const event = new Event('change');
                        Object.defineProperty(event, 'target', {
                            value: { files: e.target.files },
                            enumerable: true
                        });
                        elements.attachments.dispatchEvent(event);
                    }
                });

                tempInput.click();
            }

            function updateFileInput() {
                const dataTransfer = new DataTransfer();
                state.currentFiles.forEach(file => dataTransfer.items.add(file));
                elements.attachments.files = dataTransfer.files;
            }

            function updateFileDisplay() {
                updateFileCount();
                renderFilePreviews();
            }

            function updateFileCount() {
                elements.fileCountDisplay.textContent = state.currentFiles.length;
                elements.fileCountDisplay.classList.toggle('text-danger', state.currentFiles.length >= 5);
            }

            function renderFilePreviews() {
                if (state.currentFiles.length === 0) {
                    elements.attachmentPreviews.innerHTML = `
                        <div class="empty-state text-center py-3">
                            <i class="bi bi-cloud-arrow-up fs-1 text-muted"></i>
                            <p class="text-muted small mt-2">No files selected</p>
                        </div>
                    `;
                    return;
                }

                elements.attachmentPreviews.innerHTML = '';
                state.currentFiles.forEach((file, index) => {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'file-preview d-flex align-items-center border rounded p-2 mb-2 bg-light';
                    previewDiv.dataset.fileIndex = index;

                    // File type indicator
                    const fileTypeBadge = document.createElement('span');
                    fileTypeBadge.className = 'file-type-badge badge bg-secondary me-2 text-white';
                    fileTypeBadge.textContent = file.type.split('/')[1]?.toUpperCase() || 'FILE';

                    previewDiv.innerHTML = `
                        <div class="file-icon me-2">
                            ${file.type.startsWith('image/') ?
                            `<img src="${URL.createObjectURL(file)}" class="img-thumbnail" style="max-width: 60px; max-height: 60px">` :
                            `<i class="bi ${getFileIconClass(file)} fs-3"></i>`}
                                </div>
                                <div class="file-info flex-grow-1">
                                    <div class="file-name text-truncate" style="max-width: 200px" title="${file.name}">
                                        ${file.name}
                                    </div>
                                    <div class="file-meta d-flex justify-content-between small text-muted">
                                        <span>${formatFileSize(file.size)}</span>
                                        <span>${new Date(file.lastModified).toLocaleDateString()}</span>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-outline-danger ms-2">
                                    <i class="bi bi-trash"></i>
                                </button>
                            `;

                    // Insert type badge
                    previewDiv.insertBefore(fileTypeBadge, previewDiv.firstChild);

                    // Add click handler for removal
                    previewDiv.querySelector('button').onclick = (e) => {
                        e.preventDefault();
                        state.currentFiles.splice(index, 1);
                        updateFileInput();
                        updateFileDisplay();
                        showAlert('info', `Removed ${file.name}`);
                    };

                    // Add click handler for preview
                    if (file.type.startsWith('image/')) {
                        previewDiv.querySelector('.file-icon').style.cursor = 'pointer';
                        previewDiv.querySelector('.file-icon').onclick = (e) => {
                            e.stopPropagation();
                            showImagePreview(file);
                        };
                    }

                    elements.attachmentPreviews.appendChild(previewDiv);
                });
            }

            function showImagePreview(file) {
                // Still using modal for image preview as it's better UX
                const modalContent = `
                    <div class="text-center">
                        <img src="${URL.createObjectURL(file)}" class="img-fluid" alt="Preview">
                        <div class="mt-3">
                            <span class="badge bg-dark text-white">${file.name}</span>
                            <span class="badge bg-secondary ms-1 text-white">${formatFileSize(file.size)}</span>
                        </div>
                    </div>
                `;

                $('#imagePreviewModal .modal-body').html(modalContent);
                $('#imagePreviewModal').modal('show');
            }

            function getFileIconClass(file) {
                if (file.type.startsWith('image/')) return 'bi-file-image text-primary';

                const ext = file.name.split('.').pop().toLowerCase();
                switch (ext) {
                    case 'pdf': return 'bi-file-earmark-pdf text-danger';
                    case 'doc':
                    case 'docx': return 'bi-file-earmark-word text-primary';
                    case 'xls':
                    case 'xlsx': return 'bi-file-earmark-excel text-success';
                    case 'ppt':
                    case 'pptx': return 'bi-file-earmark-ppt text-warning';
                    case 'zip':
                    case 'rar': return 'bi-file-earmark-zip text-secondary';
                    case 'txt': return 'bi-file-earmark-text text-info';
                    case 'csv': return 'bi-file-earmark-spreadsheet text-success';
                    default: return 'bi-file-earmark text-secondary';
                }
            }

            // Notification Channel Functions
            function initializeNotificationChannels() {
                $('.channel-card').on('click', handleChannelClick);
                $('.modal-footer .btn-primary').on('click', updateChannelSelection);
            }

            function handlePriorityChange() {
                const priority = $(this).val();
                state.isCriticalPriority = priority === 'critical';

                state.selectedChannels = ['mail', 'whatsapp', 'database'];
                if (state.isCriticalPriority) {
                    state.selectedChannels.push('sms');
                }

                updateChannelSelection();
            }

            function handleChannelClick() {
                if (state.isCriticalPriority && $(this).data('channel') === 'sms') {
                    showAlert('warning', 'SMS channel is required for critical priority tickets');
                    return;
                }

                const channel = $(this).data('channel');
                const index = state.selectedChannels.indexOf(channel);

                if (index === -1) {
                    state.selectedChannels.push(channel);
                } else {
                    state.selectedChannels.splice(index, 1);
                }

                updateChannelSelection();
            }

            function updateChannelSelection() {
                updateChannelSelectionUI();
                updateChannelSelectionSummary();
            }

            function updateChannelSelectionUI() {
                $('.channel-card').each(function() {
                    const channel = $(this).data('channel');
                    const isSelected = state.selectedChannels.includes(channel);
                    const isLocked = state.isCriticalPriority && channel === 'sms';

                    $(this).toggleClass('selected', isSelected)
                        .toggleClass('locked', isLocked)
                        .find('i').toggleClass('text-muted', !isSelected && !isLocked);
                });

                $('#notification_channels_wrapper').html(
                    state.selectedChannels.map(channel =>
                        `<input type="hidden" name="notification_channels[]" value="${channel}">`
                    ).join('')
                );
            }

            function updateChannelSelectionSummary() {
                let summaryContainer = $('#channelSelectionSummary');

                if (summaryContainer.length === 0) {
                    summaryContainer = $(`
                        <div id="channelSelectionSummary" class="mt-2 channel-summary">
                            <small class="text-muted">Notification channels will be selected automatically</small>
                        </div>
                    `);
                    $('#managerSection').append(summaryContainer);
                }

                if (state.selectedChannels.length > 0) {
                    const channelBadges = state.selectedChannels.map(channel => {
                        const isLocked = state.isCriticalPriority && channel === 'sms';
                        return `<span class="badge text-white ${isLocked ? 'bg-secondary' : 'bg-primary'} me-1">
                            ${channel} ${isLocked ? '<i class="bi bi-lock-fill ms-1"></i>' : ''}
                        </span>`;
                    }).join('');

                    summaryContainer.html(`
                        <small class="text-muted">Notification channels:</small>
                        <div>${channelBadges}</div>
                        <small class="text-muted click-to-edit" style="cursor: pointer; color: #0d6efd !important;">
                            <i class="bi bi-pencil-square"></i> Click to edit
                        </small>
                    `);

                    $('.click-to-edit').on('click', () => $('#channelModal').modal('show'));
                } else {
                    summaryContainer.html('<small class="text-muted">No notification channels selected</small>');
                }
            }

            // Utility Functions
            function updateDateLabels(label, description) {
                $('#dateLabel').text(label);
                $('#dateDescription').text(description);
            }

            function showSection(id) {
                $(id).removeClass('hidden-section');
            }

            function hideSection(id) {
                $(id).addClass('hidden-section');
            }

            function showSections(ids) {
                ids.forEach(id => showSection(id));
            }

            function hideSections(ids) {
                ids.forEach(id => hideSection(id));
            }

            function clearError(id) {
                $(id).removeClass('has-error').find('.error-message').remove();
            }

            function truncateText(text, maxLength) {
                return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            function timeAgo(date) {
                const seconds = Math.floor((new Date() - date) / 1000);
                const intervals = [
                    { unit: 'year', divisor: 31536000 },
                    { unit: 'month', divisor: 2592000 },
                    { unit: 'week', divisor: 604800 },
                    { unit: 'day', divisor: 86400 },
                    { unit: 'hour', divisor: 3600 },
                    { unit: 'minute', divisor: 60 },
                    { unit: 'second', divisor: 1 }
                ];

                for (const { unit, divisor } of intervals) {
                    const interval = Math.floor(seconds / divisor);
                    if (interval >= 1) return `${interval} ${unit}${interval === 1 ? '' : 's'} ago`;
                }
                return 'Just now';
            }

            function formatTicketDate(dateString) {
                if (!dateString) return 'Unknown date';
                const now = new Date();
                const ticketDate = new Date(dateString);
                const diffInHours = Math.abs(now - ticketDate) / 36e5;

                if (ticketDate.toDateString() === now.toDateString()) {
                    if (diffInHours < 1) {
                        const mins = Math.floor(diffInHours * 60);
                        return `<span class="date-today recent-highlight">${mins}m ago</span>`;
                    }
                    return `<span class="date-today">Today at ${ticketDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>`;
                }

                const yesterday = new Date(now);
                yesterday.setDate(yesterday.getDate() - 1);
                if (ticketDate.toDateString() === yesterday.toDateString()) {
                    return `<span class="date-yesterday">Yesterday at ${ticketDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>`;
                }

                return ticketDate.toLocaleDateString([], {month: 'short', day: 'numeric', year: 'numeric'});
            }

            function getPriorityBadgeClass(priority) {
                const priorityLower = priority?.toLowerCase() || '';
                switch(priorityLower) {
                    case 'high': return 'bg-danger text-danger-fg';
                    case 'medium': return 'bg-warning text-warning-fg';
                    case 'low': return 'bg-success text-success-fg';
                    case 'critical': return 'bg-danger text-white-fg';
                    default: return 'bg-light text-dark';
                }
            }

            function getTimeBadgeClass(dateString) {
                if (!dateString) return 'bg-dark';
                const now = new Date();
                const ticketDate = new Date(dateString);
                const diffInDays = Math.abs(now - ticketDate) / (24 * 60 * 60 * 1000);

                if (diffInDays < 1) return 'bg-danger';
                if (diffInDays < 7) return 'bg-primary';
                return 'bg-dark';
            }

            function getStatusColor(status) {
                switch ((status || '').toLowerCase()) {
                    case 'open': return 'primary';
                    case 'escalated': return 'danger';
                    case 'reopen': return 'warning';
                    case 'resolved': return 'success';
                    case 'closed': return 'secondary';
                    default: return 'info';
                }
            }

            function showAlert(type = "info", message) {
                const toastOptions = {
                    timeOut: 3000,
                    positionClass: 'toast-bottom-right',
                    closeButton: true
                };

                toastr[type](message, '', toastOptions);
            }

            function showLoading(element) {
                const el = element.jquery ? element[0] : element;
                if (!el?.parentNode) return;

                const spinnerId = `${el.id}-spinner`;
                if (document.getElementById(spinnerId)) return;

                const spinner = document.createElement('span');
                spinner.className = 'loading-spinner ms-2';
                spinner.id = spinnerId;
                el.parentNode.insertBefore(spinner, el.nextSibling);
            }

            function hideLoading(element) {
                const el = element.jquery ? element[0] : element;
                if (!el) return;

                const spinner = document.getElementById(`${el.id}-spinner`);
                if (spinner) spinner.remove();
            }

            /**
             * ticket data submission
             */
            function bindFormSubmit() {
                $('#ticketForm').on('submit', function (e) {
                    e.preventDefault();
                    const form = this;

                    const submitBtn = document.getElementById('submitBtn');
                    const submitText = document.getElementById('submitText');
                    const submitSpinner = document.getElementById('submitSpinner');

                    if (!validateForm()) {
                        return;
                    }

                    submitBtn.disabled = true;
                    submitText.textContent = 'Creating Ticket...';
                    submitSpinner.style.display = 'inline-block';

                    // Prepare form data
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                    // Get all values properly
                    formData.append('saas_app_id', $('#service').val());
                    formData.append('topic_id', $('#topic').val());
                    formData.append('sub_topic_id', $('#subtopic').val());
                    formData.append('priority', $('#priority').val());
                    formData.append('title', $('#subject').val());
                    formData.append('description', $('#description').val());
                    formData.append('assigned_to', $('#manager').val());

                    // Handle client differently based on service type
                    if (state.isWorkingCustomer) {
                        const clientName = $('#clientName').val().trim();
                        formData.append('client_name', clientName);
                    } else {
                        const clientId = $('#client').val();
                        formData.append('client_id', clientId);
                    }

                    // Add conditional fields
                    const issueDate = $('#issueDate').val();
                    if (issueDate) {
                        formData.append('issue_date', issueDate);
                    }

                    const tertiaryTopic = $('#tertiaryTopic').val();
                    if (tertiaryTopic) {
                        formData.append('tertiary_topic_id', tertiaryTopic);
                    }

                    if (!document.getElementById('senderIdSection').classList.contains('hidden-section')) {
                        formData.append('sender_id', $('#senderId').val());

                        const selectedOperators = $('#operator').val() || [];
                        selectedOperators.forEach(operatorId => {
                            formData.append('operator[]', operatorId);
                        });
                    }

                    if (!document.getElementById('paymentChannelSection').classList.contains('hidden-section')) {
                        formData.append('payment_channel_id', $('#paymentChannel').val());
                    }

                    // Add notification channels
                    state.selectedChannels.forEach((channel, index) => {
                        formData.append(`notification_channels[${index}]`, channel);
                    });

                    // Add attachments
                    const files = elements.attachments.files;
                    for (let i = 0; i < files.length; i++) {
                        formData.append('attachments[]', files[i]);
                    }

                    fetch('/backend/ticket/store', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(async response => {
                            const data = await response.json();
                            if (!response.ok) {
                                if (data.errors) {
                                    const errorMessages = Object.values(data.errors).flat().join('<br>');
                                    throw new Error(errorMessages);
                                }
                                throw new Error(data.message || 'Network response was not ok');
                            }
                            return data;
                        })
                        .then(data => {
                            if (data.success) {
                                showAlert("success", data.message || 'Ticket created successfully!');
                                resetForm(form);
                            } else {
                                throw new Error(data.message || 'Failed to create ticket');
                            }
                        })
                        .catch(error => {
                            const errors = error.message.split('<br>');
                            if (errors.length > 1) {
                                errors.forEach(err => toastr.error(err));
                            } else {
                                showAlert("error", error.message || 'Error creating ticket');
                            }
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitText.textContent = 'Create Ticket';
                            submitSpinner.style.display = 'none';
                        });
                });
            }


            function validateForm() {
                let isValid = true;

                // Validate service
                if (!$('#service').val()) {
                    showAlert('error', 'Please select a service');
                    $('#service').focus();
                    isValid = false;
                }

                // Validate client based on service type
                if (state.isWorkingCustomer) {
                    if (!$('#clientName').val().trim()) {
                        showAlert('error', 'Please enter client name for Working Customer');
                        $('#clientName').focus();
                        isValid = false;
                    }
                } else if (!$('#client').val()) {
                    showAlert('error', 'Please select a client');
                    $('#client').focus();
                    isValid = false;
                }

                // Validate other required fields
                const requiredFields = [
                    { element: $('#topic'), name: 'Topic' },
                    { element: $('#subtopic'), name: 'Subtopic' },
                    { element: $('#priority'), name: 'Priority' },
                    { element: $('#manager'), name: 'Manager' },
                ];

                requiredFields.forEach(field => {
                    if (!field.element.val()) {
                        showAlert('error', `Please provide ${field.name}`);
                        if (isValid) field.element.focus(); // Only focus first invalid field
                        isValid = false;
                    }
                });

                return isValid;
            }

            /**
             * Clear form after successful submission
             */
            function resetForm(form) {
                // Prevent any recursive triggers
                $('.select2').off('change');

                // Reset the basic form
                form.reset();

                // Reset state
                state.currentFiles = [];
                state.selectedChannels = ['mail', 'whatsapp', 'database'];
                state.isCriticalPriority = false;
                state.isWorkingCustomer = false;

                // Reset UI elements
                updateFileDisplay();
                updateChannelSelection();

                // Clear inputs
                $('#clientName').val('');
                $('#subject').val('');
                $('#description').val('');

                // Reset Select2 dropdowns carefully
                $('.select2').each(function() {
                    const $el = $(this);
                    if ($el.attr('id') !== 'service' || !state.preSelectedServiceId) {
                        $el.val(null).trigger('change.select2');
                    }
                });

                // Reset priority
                $('#priority').val('low').trigger('change');

                hideSections([
                    '#clientSection',
                    '#topicSection',
                    '#subtopicSection',
                    '#tertiaryTopicSection',
                    '#subjectSection',
                    '#descriptionSection',
                    '#prioritySection',
                    '#attachmentsSection',
                    '#ticketHistorySection',
                    '#senderIdSection',
                    '#operatorSection',
                    '#paymentChannelSection',
                    '#dateSection',
                    '#managerSection',
                    '#submitSection'
                ]);

                showSection('#serviceSection');

                // Rebind event handlers
                setTimeout(() => {
                    elements.service.off('change').on('change', handleServiceChange);
                    elements.client.off('change').on('change', handleClientChange);
                }, 100);
            }
        });
    </script>
@endpush
