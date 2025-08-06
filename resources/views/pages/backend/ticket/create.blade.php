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
                        <select class="form-select form-control select2" id="client" required >
                            <option value="" >Select a client</option>
                            @if(isset($preSelectedClient))
                                <option value="{{ $preSelectedClient->id }}" selected>{{ $preSelectedClient->name }}</option>
                            @endif
                        </select>
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
        // Ticket System Script
        $(document).ready(function () {
            let currentFiles = [];
            let selectedChannels = [];
            let isCriticalPriority = false;
            const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
            const ALLOWED_FILE_TYPES = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

            const urlParams = new URLSearchParams(window.location.search);
            const preSelectedServiceId = urlParams.get('saas_app_id');

            initialize();

            if (preSelectedServiceId) $('#service').trigger('change');

            function initialize() {
                initializeSelect2();
                bindServiceChange();
                bindClientChange();
                bindTopicChange();
                bindSubtopicChange();
                initializeFileUpload();
                initializeNotificationChannels();
                bindFormSubmit();
            }

            function initializeSelect2() {
                $('.select2').select2({ width: '100%' });
                $('.select2-ajax').select2({
                    width: '100%',
                    ajax: {
                        url: $('#service').data('ajax-url'),
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

            function bindServiceChange() {
                $('#service').on('change', function () {
                    const serviceId = $(this).val();
                    if (serviceId) {
                        loadClients(serviceId);
                        showSection('#clientSection');
                        clearError('#serviceSection');
                    } else {
                        hideSections(['#clientSection', '#topicSection']);
                    }
                });
            }

            function loadClients(serviceId) {
                const preSelectedClientId = urlParams.get('client_id');
                $.get(`/backend/client/client_by_services/${serviceId}`)
                    .done(({ data }) => {
                        const $client = $('#client').empty().append('<option value="">Select a client</option>');
                        data.forEach(client => {
                            const selected = preSelectedClientId == client.id ? 'selected' : '';
                            $client.append(`<option value="${client.id}" ${selected}>${client.name}</option>`);
                        });
                        if (preSelectedClientId) $('#client').trigger('change');
                    })
                    .fail(() => $('#client').html('<option>Error loading clients</option>'));
            }

            function bindClientChange() {
                $('#client').on('change', function () {
                    const clientId = $(this).val();
                    if (clientId) {
                        loadClientTicketHistory(clientId);
                        loadTopics($('#service').val());
                        clearError('#clientSection');
                    } else {
                        hideSections(['#topicSection', '#ticketHistorySection']);
                    }
                });
            }

            async function loadClientTicketHistory(clientId) {
                try {
                    const response = await fetch(`/backend/ticket/client_ticket_history/${clientId}`);
                    const responseData = await response.json();
                    const ticketHistoryDiv = document.getElementById('ticketHistory');

                    const tickets = responseData?.data?.data;
                    if (!Array.isArray(tickets)) {
                        throw new Error('Invalid data format received from server');
                    }

                    showSection('#ticketHistorySection');

                    if (ticketHistoryDiv) {
                        renderTicketHistory(tickets, clientId);
                    }
                } catch (error) {
                    const ticketHistoryDiv = document.getElementById('ticketHistory');
                    if (ticketHistoryDiv) {
                        ticketHistoryDiv.innerHTML = `
                            <div class="alert alert-danger m-3">
                                <i class="bi bi-exclamation-triangle-fill"></i> Failed to load ticket history: ${error.message}
                            </div>
                        `;
                    }
                    hideSection('#ticketHistorySection');
                }
            }

            function renderTicketHistory(tickets, clientId) {
                const ticketHistoryDiv = document.getElementById('ticketHistory');
                if (!ticketHistoryDiv) return;

                if (tickets.length === 0) {
                    ticketHistoryDiv.innerHTML = `
                        <div class="empty-state text-center py-5">
                            <i class="bi bi-inbox-fill empty-state-icon fs-1 text-muted"></i>
                            <h4 class="empty-state-title mt-3">No Previous Tickets</h4>
                            <p class="empty-state-text text-muted">This client hasn't submitted any tickets yet.</p>
                        </div>
                        `;
                    return;
                }

                const clientName = tickets[0]?.client?.name || 'Client';
                const ticketsToShow = tickets.slice(0, 5);

                ticketHistoryDiv.innerHTML = `
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
                    const viewAll = document.createElement('a');
                    viewAll.href = `/backend/ticket/client_ticket_history/${client?.uid}`;
                    viewAll.target = '_blank';
                    viewAll.className = 'btn btn-outline-primary w-100 mt-3 d-flex align-items-center justify-content-center gap-2';
                    viewAll.innerHTML = 'View All Tickets <i class="bi bi-arrow-right"></i>';
                    ticketHistoryDiv.appendChild(viewAll);
                }
            }

            function createTicketItem(ticket) {
                const createdDate = ticket.created_at ? new Date(ticket.created_at) : new Date();
                const isResolved = ticket.status?.toLowerCase() === 'resolved';

                let categoryPath = ticket.topic?.name;
                if (ticket.subtopic?.name) categoryPath += ` › ${ticket.subtopic.name}`;
                if (ticket.tertiary_topic?.name) categoryPath += ` › ${ticket.tertiary_topic.name}`;

                const item = document.createElement('div');
                item.className = `ticket-history-item mb-3 p-3 rounded-3 border ${isResolved ? 'bg-light' : 'bg-white'} shadow-sm`;
                item.style.transition = 'all 0.2s ease';
                item.style.cursor = 'pointer';

                // hover effect
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

                // click handler to view ticket details
                item.addEventListener('click', () => {
                    window.location.href = `/backend/ticket/view/${ticket.uid}`;
                });

                return item;
            }
            function truncateText(text, maxLength) {
                return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
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


            function bindTopicChange() {
                $('#topic').on('change', function () {
                    const topicId = $(this).val();
                    if (!topicId) return hideSections(['#subtopicSection', '#dateSection']);

                    loadSubtopics(topicId);

                    const topicName = $(this).find('option:selected').text().toLowerCase();
                    if (topicName.includes('sms')) {
                        loadSenderIds($('#client').val());
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
                });
            }

            function loadTopics(serviceId) {
                $.get(`/backend/topic/get_by_service/${serviceId}`)
                    .done(({ data }) => {
                        const $topic = $('#topic').empty().append('<option value="">Select a topic</option>');
                        data.forEach(t => $topic.append(`<option value="${t.id}">${t.name}</option>`));
                        showSection('#topicSection');
                    })
                    .fail(() => showAlert('error', 'Failed to load topics'));
            }

            function bindSubtopicChange() {
                $('#subtopic').on('change', function () {
                    const subtopicId = $(this).val();
                    if (!subtopicId) return hideSections(['#subjectSection']);

                    loadTertiaryTopics(subtopicId);
                    $('#priority').val('low').trigger('change');
                    showAlert("info", 'Priority automatically set to "low"');
                    showSections(['#subjectSection', '#descriptionSection', '#prioritySection', '#attachmentsSection', '#submitSection']);
                    loadManagers();
                    clearError('#subtopicSection');
                });
            }

            function loadSubtopics(topicId) {
                $.get(`/backend/subtopic/get_by_topic_id/${topicId}`)
                    .done(({ data }) => {
                        const $sub = $('#subtopic').empty().append('<option value="">Select a subtopic</option>');
                        data.forEach(st => $sub.append(`<option value="${st.id}">${st.name}</option>`));
                        showSection('#subtopicSection');
                    })
                    .fail(() => showAlert('error', 'Failed to load subtopics'));
            }

            function loadTertiaryTopics(subtopicId) {
                $.get(`/backend/tertiary/tertiary_topic_by_subtopic_id/${subtopicId}`)
                    .done(({ data }) => {
                        const $ter = $('#tertiaryTopic').empty().append('<option value="">Select a tertiary topic (optional)</option>');
                        data.forEach(t => $ter.append(`<option value="${t.id}">${t.name}</option>`));
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

            function initializeFileUpload() {
                const attachmentsInput = document.getElementById('attachments');
                const addMoreBtn = document.getElementById('addMoreFiles');
                const fileCountDisplay = document.getElementById('fileCount');
                const attachmentPreviews = document.getElementById('attachmentPreviews');

                // Handle file selection
                attachmentsInput.addEventListener('change', function(event) {
                    const newFiles = Array.from(event.target.files);

                    // Validate files before adding
                    const validFiles = newFiles.filter(file => {
                        if (file.size > MAX_FILE_SIZE) {
                            showAlert('error', `File "${file.name}" exceeds 2MB limit`);
                            return false;
                        }
                        if (!ALLOWED_FILE_TYPES.includes(file.type)) {
                            showAlert('error', `File type not supported for "${file.name}"`);
                            return false;
                        }
                        return true;
                    });

                    // Check for duplicates
                    const uniqueFiles = validFiles.filter(newFile =>
                        !currentFiles.some(
                            file => file.name === newFile.name &&
                                file.size === newFile.size &&
                                file.lastModified === newFile.lastModified
                        )
                    );

                    currentFiles = [...currentFiles, ...uniqueFiles];
                    updateFileInput();
                    updateFileDisplay();
                });

                // Handle "Add More" button click
                addMoreBtn.addEventListener('click', function() {
                    const tempInput = document.createElement('input');
                    tempInput.type = 'file';
                    tempInput.multiple = true;
                    tempInput.accept = ALLOWED_FILE_TYPES.join(',');

                    tempInput.addEventListener('change', (e) => {
                        if (e.target.files.length > 0) {
                            const event = new Event('change');
                            Object.defineProperty(event, 'target', {
                                value: { files: e.target.files },
                                enumerable: true
                            });
                            attachmentsInput.dispatchEvent(event);
                        }
                    });

                    tempInput.click();
                });

                function updateFileInput() {
                    const dataTransfer = new DataTransfer();
                    currentFiles.forEach(file => dataTransfer.items.add(file));
                    attachmentsInput.files = dataTransfer.files;
                }

                function updateFileDisplay() {
                    updateFileCount();
                    renderFilePreviews();
                }

                function updateFileCount() {
                    fileCountDisplay.textContent = currentFiles.length;
                }

                function renderFilePreviews() {
                    if (currentFiles.length === 0) {
                        attachmentPreviews.innerHTML = '<p class="text-muted small">No files selected</p>';
                        return;
                    }

                    attachmentPreviews.innerHTML = '';
                    currentFiles.forEach((file, index) => {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'file-preview d-flex align-items-center border rounded p-2 mb-2 bg-light';
                        previewDiv.dataset.fileIndex = index;

                        // File icon/thumbnail
                        const previewIcon = document.createElement('div');
                        previewIcon.className = 'file-icon me-2';

                        if (file.type.startsWith('image/')) {
                            const img = document.createElement('img');
                            img.src = URL.createObjectURL(file);
                            img.className = 'img-thumbnail';
                            img.style.maxWidth = '60px';
                            img.style.maxHeight = '60px';
                            previewIcon.appendChild(img);
                        } else {
                            const icon = document.createElement('i');
                            icon.className = `bi ${getFileIconClass(file)} fs-3`;
                            previewIcon.appendChild(icon);
                        }

                        // File info
                        const fileInfo = document.createElement('div');
                        fileInfo.className = 'file-info flex-grow-1';
                        fileInfo.innerHTML = `
                            <div class="file-name text-truncate" style="max-width: 200px">${file.name}</div>
                            <div class="file-size small text-muted">${formatFileSize(file.size)}</div>
                        `;

                        // Remove button
                        const removeBtn = document.createElement('button');
                        removeBtn.className = 'btn btn-sm btn-outline-danger ms-2';
                        removeBtn.innerHTML = '<i class="bi bi-trash"></i>';
                        removeBtn.onclick = (e) => {
                            e.preventDefault();
                            currentFiles.splice(index, 1);
                            updateFileInput();
                            updateFileDisplay();
                        };

                        previewDiv.appendChild(previewIcon);
                        previewDiv.appendChild(fileInfo);
                        previewDiv.appendChild(removeBtn);
                        attachmentPreviews.appendChild(previewDiv);
                    });
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
                        default: return 'bi-file-earmark text-secondary';
                    }
                }

                function formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                }
            }

            function updateDateLabels(label, description) {
                $('#dateLabel').text(label);
                $('#dateDescription').text(description);
            }

            /**
             * notification channels
             */
            function initializeNotificationChannels() {
                // Initialize channel selection when priority changes
                $('#priority').on('change', handlePriorityChange);

                // Set up channel card click handlers
                $('.channel-card').on('click', function() {
                    if (isCriticalPriority && $(this).data('channel') === 'sms') {
                        showAlert('warning', 'SMS channel is required for critical priority tickets');
                        return;
                    }

                    const channel = $(this).data('channel');
                    const index = selectedChannels.indexOf(channel);

                    if (index === -1) {
                        selectedChannels.push(channel);
                    } else {
                        selectedChannels.splice(index, 1);
                    }

                    updateChannelSelectionUI();
                    updateChannelSelectionSummary();
                });

                // Set up modal confirm button
                $('.modal-footer .btn-primary').on('click', function() {
                    updateChannelSelectionUI();
                    updateChannelSelectionSummary();
                });
            }

            function handlePriorityChange() {
                const priority = $(this).val();
                isCriticalPriority = priority === 'critical';

                // Auto-select default channels
                selectedChannels = ['mail', 'whatsapp', 'database'];
                if (isCriticalPriority) {
                    selectedChannels.push('sms');
                }

                updateChannelSelectionUI();
                updateChannelSelectionSummary();
            }

            function updateChannelSelectionUI() {
                $('.channel-card').each(function() {
                    const channel = $(this).data('channel');
                    const isSelected = selectedChannels.includes(channel);
                    const isLocked = isCriticalPriority && channel === 'sms';

                    $(this).toggleClass('selected', isSelected);
                    $(this).find('i').toggleClass('text-muted', !isSelected);

                    if (isLocked) {
                        $(this).addClass('locked');
                        $(this).find('i').removeClass('text-muted');
                    } else {
                        $(this).removeClass('locked');
                    }
                });

                // Update hidden input fields for form submission
                $('#notification_channels_wrapper').html(
                    selectedChannels.map(channel =>
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

                if (selectedChannels.length > 0) {
                    const channelBadges = selectedChannels.map(channel => {
                        const isLocked = isCriticalPriority && channel === 'sms';
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

                    $('.click-to-edit').on('click', function() {
                        $('#channelModal').modal('show');
                    });
                } else {
                    summaryContainer.html('<small class="text-muted">No notification channels selected</small>');
                }
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

            function timeAgo(dateString) {
                if (!dateString) return '';

                const date = new Date(dateString);
                const now = new Date();
                const seconds = Math.floor((now - date) / 1000);

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
                    if (interval >= 1) {
                        return `${interval} ${unit}${interval === 1 ? '' : 's'} ago`;
                    }
                }

                return 'Just now';
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
                switch (type) {
                    case 'success':
                        toastr.success(message, '', toastOptions);
                        break;
                    case 'error':
                        toastr.error(message, '', toastOptions);
                        break;
                    case 'warning':
                        toastr.warning(message, '', toastOptions);
                        break;
                    default:
                        toastr.info(message, '', toastOptions);
                }
            }

            function bindFormSubmit() {
                // Bind form submission if needed
            }
        });

    </script>
@endpush
