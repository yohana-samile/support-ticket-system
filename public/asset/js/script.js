    class TicketForm {
        constructor() {
            this.initElements();
            this.initState();
            this.initSelect2();
            this.initEventListeners();
            this.loadInitialData();
        }

        initSelect2() {
            try{
                // Initialize service select first
                if (this.serviceSelect) {
                    this.serviceSelect2 = $(this.serviceSelect).select2({
                        ajax: {
                            url: this.serviceSelect.dataset.ajaxUrl,
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    search: params.term,
                                    page: params.page
                                };
                            },
                            processResults: function (data, params) {
                                params.page = params.page || 1;
                                return {
                                    results: data.data.map(item => ({
                                        id: item.id,
                                        text: item.name
                                    })),
                                    pagination: {
                                        more: (params.page * 10) < data.total
                                    }
                                };
                            },
                            cache: true
                        },
                        placeholder: this.serviceSelect.dataset.placeholder,
                        minimumInputLength: 1,
                        width: '100%'
                    });
                }

                // Initialize client select with proper container class
                if (this.clientSelect) {
                    $(this.clientSelect).select2({
                        width: '100%',
                        dropdownParent: $(this.clientSelect).parent()
                    });
                }

                // Initialize other Select2 elements
                if (this.topicSelect) $(this.topicSelect).select2({ width: '100%' });
                if (this.subtopicSelect) $(this.subtopicSelect).select2({ width: '100%' });
                if (this.tertiaryTopicSelect) $(this.tertiaryTopicSelect).select2({ width: '100%' });
                if (this.senderIdSelect) $(this.senderIdSelect).select2({ width: '100%' });
                if (this.paymentChannelSelect) $(this.paymentChannelSelect).select2({ width: '100%' });
                if (this.priorityField) $(this.priorityField).select2({ width: '100%' });
                if (this.managerSelect) $(this.managerSelect).select2({ width: '100%' });

            } catch (e) {
                console.error('Select2 initialization error:', e);
            }
        }

        // Initialize DOM elements
        initElements() {
            this.form = document.getElementById('ticketForm');
            this.serviceSelect = document.getElementById('service');
            this.clientSelect = document.getElementById('client');
            this.topicSelect = document.getElementById('topic');
            this.subtopicSelect = document.getElementById('subtopic');
            this.tertiaryTopicSelect = document.getElementById('tertiaryTopic');
            this.managerSelect = document.getElementById('manager');
            this.priorityField = document.getElementById('priority');
            this.subjectField = document.getElementById('subject');
            this.descriptionField = document.getElementById('description');
            this.attachmentsInput = document.getElementById('attachments');
            this.attachmentPreviews = document.getElementById('attachmentPreviews');
            this.fileCountDisplay = document.getElementById('fileCount');
            this.addMoreFilesBtn = document.getElementById('addMoreFiles');
            this.senderIdSelect = document.getElementById('senderId');
            this.paymentChannelSelect = document.getElementById('paymentChannel');
            this.dateSection = document.getElementById('dateSection');
            this.issueDateInput = document.getElementById('issueDate');

            this.operatorChoices = new Choices('#operator', {
                removeItemButton: true,
                placeholder: true,
                searchEnabled: true,
                shouldSort: false,
                duplicateItemsAllowed: false
            });
        }

        // Initialize application state
        initState() {
            this.selectedChannels = [];
            this.isCriticalPriority = false;
            this.currentFiles = [];
            this.selectedServiceId = null;
            this.selectedClientId = null;
            this.selectedTopicId = null;
            this.selectedSubtopicId = null;
        }

        // Set up event listeners
        initEventListeners() {
            this.serviceSelect.addEventListener('change', () => this.handleServiceChange());
            this.clientSelect.addEventListener('change', () => this.handleClientChange());
            this.topicSelect.addEventListener('change', () => this.handleTopicChange());
            this.subtopicSelect.addEventListener('change', () => this.handleSubtopicChange());
            this.tertiaryTopicSelect.addEventListener('change', () => this.handleTertiaryTopicChange());
            this.subjectField.addEventListener('input', () => this.handleSubjectInput());
            this.descriptionField.addEventListener('input', () => this.handleDescriptionInput());
            this.priorityField.addEventListener('change', () => this.handlePriorityChange());
            this.attachmentsInput.addEventListener('change', (e) => this.handleFileSelect(e));
            this.addMoreFilesBtn.addEventListener('click', () => this.handleAddMoreFiles());
            this.form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }

        // Load initial data if preselected service exists
        loadInitialData() {
            const urlParams = new URLSearchParams(window.location.search);
            const preSelectedServiceId = urlParams.get('saas_app_id');
            this.ticketHistoryDiv = document.getElementById('ticketHistory');

            // Call loadServices with the preselected ID
            this.loadServices(preSelectedServiceId);
        }

        // Main form handler methods
        async handleServiceChange() {
            this.selectedServiceId = this.serviceSelect.value;
            if (!this.selectedServiceId) return;

            const selectedServiceText = this.serviceSelect.options[this.serviceSelect.selectedIndex].text;
            const isWorkingCustomer = selectedServiceText.toLowerCase() === 'working customer';

            if (isWorkingCustomer) {
                this.setupWorkingCustomerFlow();
            } else {
                this.setupRegularClientFlow();
            }

            this.hideDependentSections();
            await this.loadClients();
        }

        setupWorkingCustomerFlow() {
            let customerNameInput = document.getElementById('customerNameInput');
            if (!customerNameInput) {
                customerNameInput = this.createCustomerNameInput();

                // Get the parent container of the Select2 element
                const select2Container = $(this.clientSelect).closest('.form-section');

                // Append the input to the same container
                select2Container.append(customerNameInput);
            }

            // Hide the Select2 container
            $(this.clientSelect).select2('container').hide();
            customerNameInput.style.display = 'block';
        }

        createCustomerNameInput() {
            const input = document.createElement('input');
            input.id = 'customerNameInput';
            input.type = 'text';
            input.className = 'form-control mt-2';
            input.placeholder = 'Enter customer full name';
            input.required = true;
            input.style.display = 'none'; // Start hidden

            input.addEventListener('input', () => {
                if (input.value.trim() !== '') {
                    this.showSection('topicSection');
                    this.loadTopics();
                } else {
                    this.hideSection('topicSection');
                }
            });

            return input;
        }

        setupRegularClientFlow() {
            this.showSection('clientSection');

            const customerNameInput = document.getElementById('customerNameInput');
            if (customerNameInput) {
                customerNameInput.style.display = 'none';
            }

            // Safely handle Select2
            if (this.clientSelect) {
                // Ensure Select2 is initialized
                if (!$.fn.select2) {
                    console.error('Select2 not loaded');
                    return;
                }

                // Initialize Select2 if not already initialized
                if (!$(this.clientSelect).hasClass('select2-hidden-accessible')) {
                    $(this.clientSelect).select2({
                        width: '100%',
                        dropdownParent: $(this.clientSelect).parent()
                    });
                }

                // Show the Select2 container safely
                const select2Instance = $(this.clientSelect).data('select2');
                if (select2Instance && select2Instance.$container) {
                    select2Instance.$container.show();
                }

                this.clientSelect.style.display = 'block';
                this.clientSelect.innerHTML = '<option value="" selected disabled>Loading clients...</option>';
            }
        }

        hideDependentSections() {
            const sectionsToHide = [
                'subtopicSection', 'tertiaryTopicSection',
                'managerSection', 'senderIdSection',
                'operatorSection', 'paymentChannelSection', 'subjectSection',
                'descriptionSection', 'prioritySection', 'attachmentsSection',
                'submitSection'
            ];

            sectionsToHide.forEach(section => this.hideSection(section));
        }

        async loadServices(preSelectedServiceId = null) {
            try {
                const response = await fetch('/backend/saas_app/search');
                const data = await response.json();

                // Clear existing options
                $(this.serviceSelect).empty();

                // Add default option
                const defaultOption = new Option('Select a Saas App', '', true, true);
                defaultOption.disabled = true;
                this.serviceSelect.appendChild(defaultOption);

                // Add all services
                data.data.forEach(service => {
                    const option = new Option(service.name, service.id);
                    this.serviceSelect.appendChild(option);
                });

                // If there's a preselected service, set it
                if (preSelectedServiceId) {
                    $(this.serviceSelect).val(preSelectedServiceId).trigger('change');
                    this.handleServiceChange();
                }

                // Initialize Select2 after options are loaded
                $(this.serviceSelect).select2({
                    ajax: {
                        url: this.serviceSelect.dataset.ajaxUrl,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                search: params.term,
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.data.map(item => ({
                                    id: item.id,
                                    text: item.name
                                })),
                                pagination: {
                                    more: (params.page * 10) < data.total
                                }
                            };
                        },
                        cache: true
                    },
                    placeholder: this.serviceSelect.dataset.placeholder,
                    minimumInputLength: 1,
                    width: '100%'
                });

            } catch (error) {
                console.error('Error loading services:', error);
                this.serviceSelect.innerHTML = '<option value="" selected disabled>Error loading services</option>';
            }
        }

        async loadClients() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const preSelectedClientId = urlParams.get('client_id');

                const response = await fetch(`/backend/client/client_by_services/${this.selectedServiceId}`);
                const data = await response.json();

                this.resetSelect(this.clientSelect, 'Select a client');

                data.data.forEach(client => {
                    const option = document.createElement('option');
                    option.value = client.id;
                    option.textContent = client.name;

                    if (preSelectedClientId && client.id == preSelectedClientId) {
                        option.selected = true;
                        this.selectedClientId = client.id;
                        setTimeout(() => {
                            this.handleClientChange();
                            $(this.clientSelect).trigger('change');
                        }, 100);
                    }

                    this.clientSelect.appendChild(option);
                });
            } catch (error) {
                this.clientSelect.innerHTML = '<option value="" selected disabled>Error loading clients</option>';
            }
        }

        async handleClientChange() {
            this.selectedClientId = this.clientSelect.value;
            if (!this.selectedClientId) return;

            this.showLoading(this.clientSelect);
            this.resetDependentFields();

            this.showSection('topicSection');
            this.topicSelect.innerHTML = '<option value="" selected disabled>Loading topics...</option>';

            await Promise.all([
                this.loadTicketHistory(),
                this.loadTopics()
            ]);

            this.hideLoading(this.clientSelect);
        }

        resetDependentFields() {
            // Reset topic and all subsequent fields
            $(this.topicSelect).val(null).trigger('change');
            $(this.subtopicSelect).val(null).trigger('change');
            $(this.tertiaryTopicSelect).val(null).trigger('change');
            $(this.managerSelect).val(null).trigger('change');
            $(this.priorityField).val(null).trigger('change');
            this.subjectField.value = "";
            this.descriptionField.value = "";

            // Clear date field
            this.clearDateField();

            // Reset Choices.js for operator selection if it exists
            if (this.operatorChoices) {
                this.operatorChoices.clearStore();
                this.operatorChoices.setChoices(
                    [{value: '', label: 'Select mobile operator(s)', disabled: true}],
                    'value',
                    'label',
                    true
                );
            }

            // Reset any other special fields
            if (this.senderIdSelect) {
                $(this.senderIdSelect).val(null).trigger('change');
            }
            if (this.paymentChannelSelect) {
                $(this.paymentChannelSelect).val(null).trigger('change');
            }

            // Clear attachments
            this.currentFiles = [];
            this.updateFileDisplay();

            // Hide all dependent sections except topic section
            this.hideDependentSections();

            // Ensure ticket history remains visible
            this.showSection('ticketHistorySection');
        }

        clearDateField() {
            if (this.issueDateInput) {
                this.issueDateInput.value = '';
            }
            const dateSection = document.getElementById('dateSection');
            if (dateSection) {
                dateSection.classList.remove('visible-section');
                dateSection.classList.add('hidden-section');
            }
        }

        async loadTicketHistory() {
            try {
                const response = await fetch(`/backend/ticket/client_ticket_history/${this.selectedClientId}`);
                const responseData = await response.json();

                if (!responseData?.data?.data || !Array.isArray(responseData.data.data)) {
                    throw new Error('Invalid data format received from server');
                }

                this.showSection('ticketHistorySection');
                if (this.ticketHistoryDiv) {
                    this.renderTicketHistory(responseData.data);
                }
            } catch (error) {
                this.ticketHistoryDiv.innerHTML = `
                  <div class="alert alert-danger m-3">
                    <i class="bi bi-exclamation-triangle-fill"></i> Failed to load ticket history: ${error.message}
                  </div>
                `;
                this.hideSection('ticketHistorySection');
            }
        }

        renderTicketHistory({ data: tickets, total }) {
            if (!this.ticketHistoryDiv) return;

            if (tickets.length === 0) {
                this.ticketHistoryDiv.innerHTML = `
                  <div class="empty-state">
                    <i class="bi bi-inbox empty-state-icon"></i>
                    <p class="empty-state-text">No previous tickets found for this client.</p>
                  </div>
                `;
                return;
            }

            const clientName = tickets[0]?.client?.name || 'Client';
            const ticketsToShow = tickets.slice(0, 5);

            this.ticketHistoryDiv.innerHTML = `
                <div class="ticket-history-header">
                  <h3 class="ticket-history-title">
                    <span class="badge bg-primary client-badge">${clientName}</span>
                    Recent Tickets
                  </h3>
                  <span class="ticket-history-count">${Math.min(tickets.length, 5)} of ${total}</span>
                </div>
                <div id="ticketHistoryList"></div>
              `;

            const list = document.getElementById('ticketHistoryList');
            ticketsToShow.forEach(ticket => {
                list.appendChild(this.createTicketItem(ticket));
            });

            if (tickets.length > 5) {
                const viewAll = document.createElement('a');
                viewAll.href = `/backend/ticket/client_ticket_history/${this.selectedClientId}`;
                viewAll.className = 'view-all-tickets';
                viewAll.innerHTML = 'View all tickets <i class="bi bi-chevron-right"></i>';
                list.appendChild(viewAll);
            }
        }

        createTicketItem(ticket) {
            const priorityClass = ticket.priority?.toLowerCase() || 'low';
            const statusClass = ticket.status?.toLowerCase() || 'open';
            const timeAgoResult = ticket.created_at ? this.timeAgo(ticket.created_at) : '';
            const timeBadgeClass = this.getTimeBadgeClass(ticket.created_at);

            let categoryPath = ticket.topic?.name || 'No topic';
            if (ticket.subtopic?.name) categoryPath += ` › ${ticket.subtopic.name}`;
            if (ticket.tertiary_topic?.name) categoryPath += ` › ${ticket.tertiary_topic.name}`;

            const item = document.createElement('div');
            item.className = `ticket-item ${this.isTicketRecent(ticket.created_at) ? 'recent-ticket' : ''}`;

            item.innerHTML = `
                <div class="ticket-main-info">
                  <h4 class="ticket-title">${ticket.title || 'No title'}</h4>
                  <span class="ticket-status ${statusClass}">${ticket.status || 'Unknown'}</span>
                </div>
                <div class="ticket-meta">
                  <span class="ticket-category">${categoryPath}</span>
                  <span class="ticket-assignee">
                    <i class="bi bi-person"></i> ${ticket.assigned_to?.name || 'Unassigned'}
                  </span>
                  <span class="ticket-date">
                    <i class="bi bi-calendar"></i> ${this.formatTicketDate(ticket.created_at)}
                  </span>
                  <span class="ticket-number">
                    <i class="bi bi-tag"></i> ${ticket.ticket_number || ''}
                  </span>
                </div>
                ${ticket.description ? `
                    <div class="ticket-description">
                      ${ticket.description}
                    </div>
                ` : ''}
                <div class="ticket-footer">
                  <span class="ticket-priority ${priorityClass}">${ticket.priority || 'Unknown'}</span>
                  <span class="ticket-time text-white badge ${timeBadgeClass}">
                    <i class="bi bi-clock"></i> ${timeAgoResult}
                  </span>
                </div>
              `;

            return item;
        }

        async loadTopics() {
            try {
                const response = await fetch(`/backend/topic/get_by_service/${this.selectedServiceId}`);
                const data = await response.json();

                this.resetSelect(this.topicSelect, 'Select a topic');
                data.data.forEach(topic => {
                    const option = document.createElement('option');
                    option.value = topic.id;
                    option.textContent = topic.name;
                    this.topicSelect.appendChild(option);
                });
            } catch (error) {
                this.topicSelect.innerHTML = '<option value="" selected disabled>Error loading topics</option>';
            }
        }

        async handleTopicChange() {
            this.selectedTopicId = this.topicSelect.value;
            if (!this.selectedTopicId) return;

            // Clear the date field when topic changes
            this.clearDateField();

            // Get the selected option text properly for Select2
            const selectedOption = $(this.topicSelect).select2('data')[0];
            const selectedTopicText = selectedOption ? selectedOption.text.toLowerCase() : this.topicSelect.options[this.topicSelect.selectedIndex].text.toLowerCase();

            const isPaymentTopic = selectedTopicText.includes('payment') ||
                selectedTopicText.includes('billing') ||
                selectedTopicText.includes('invoice');
            const isSmsTopic = selectedTopicText.includes('sms') ||
                selectedTopicText.includes('message') ||
                selectedTopicText.includes('delivery');

            this.hideDependentSections();

            if (isSmsTopic) {
                await this.handleSmsTopic();
            } else if (isPaymentTopic) {
                await this.handlePaymentTopic();
            } else {
                await this.loadSubtopics();
            }
        }

        async handleSmsTopic() {
            this.showSection('senderIdSection');
            this.senderIdSelect.innerHTML = '<option value="" selected disabled>Loading sender IDs...</option>';

            if (!this.selectedClientId) {
                this.senderIdSelect.innerHTML = '<option value="" selected disabled>Please select a client first</option>';
                return;
            }

            try {
                const response = await fetch(`/backend/sender_id/active_sender_ids/${this.selectedClientId}`);
                const data = await response.json();

                this.resetSelect(this.senderIdSelect, 'Select a sender ID');
                data.data.forEach(sender => {
                    const option = document.createElement('option');
                    option.value = sender.id;
                    option.textContent = `${sender.sender_id}`;
                    option.dataset.operators = JSON.stringify(sender.operators || []);
                    this.senderIdSelect.appendChild(option);
                });

                this.senderIdSelect.addEventListener('change', () => this.handleSenderIdChange());
                this.showDateSection('When did the SMS fail to deliver?');
            } catch (error) {
                this.senderIdSelect.innerHTML = '<option value="" selected disabled>Error loading sender IDs</option>';
            }
        }

        async handlePaymentTopic() {
            this.showSection('paymentChannelSection');
            this.paymentChannelSelect.innerHTML = '<option value="" selected disabled>Loading payment channels...</option>';

            try {
                const response = await fetch('/backend/payment_channel/active_payment_channels');
                const data = await response.json();

                this.resetSelect(this.paymentChannelSelect, 'Select a payment channel');
                data.data.forEach(channel => {
                    const option = document.createElement('option');
                    option.value = channel.id;
                    option.textContent = channel.name;
                    this.paymentChannelSelect.appendChild(option);
                });

                this.paymentChannelSelect.addEventListener('change', () => {
                    if (this.paymentChannelSelect.value) {
                        this.loadSubtopics();
                    }
                });

                this.showDateSection('When was the payment made?');
            } catch (error) {
                this.paymentChannelSelect.innerHTML = '<option value="" selected disabled>Error loading channels</option>';
            }
        }

        showDateSection(labelText) {
            let dateSection = document.getElementById('dateSection');
            if (!dateSection) {
                dateSection = document.createElement('div');
                dateSection.id = 'dateSection';
                dateSection.className = 'mb-3 form-section';
                dateSection.innerHTML = `
                  <label for="issueDate" class="form-label">${labelText}</label>
                  <input type="date" class="form-control" id="issueDate" max="${new Date().toISOString().split('T')[0]}">
                  <div class="form-text">Please specify the date when the issue occurred</div>
                `;
                document.getElementById('topicSection').after(dateSection);
                // Store reference to the input element
                this.issueDateInput = document.getElementById('issueDate');
            } else {
                dateSection.querySelector('label').textContent = labelText;
            }

            this.showSection('dateSection');
        }

        async handleSenderIdChange() {
            if (!this.senderIdSelect.value) {
                this.hideSection('operatorSection');
                this.hideSection('subtopicSection');
                return;
            }

            this.showSection('operatorSection');
            this.operatorChoices.clearStore();
            this.operatorChoices.setChoices(
                [{value: '', label: 'Loading operators...', disabled: true}],
                'value',
                'label',
                true
            );

            try {
                const response = await fetch('/backend/operator/get_all_operator');
                const data = await response.json();

                if (data.data?.length > 0) {
                    this.operatorChoices.setChoices(
                        data.data.map(operator => ({
                            value: operator.id,
                            label: operator.name
                        })),
                        'value',
                        'label',
                        true
                    );

                    this.operatorChoices.passedElement.element.addEventListener('change', () => {
                        const selectedOperators = this.operatorChoices.getValue(true);
                        if (selectedOperators?.length > 0) {
                            this.loadSubtopics();
                        } else {
                            this.hideSection('subtopicSection');
                        }
                    }, false);
                } else {
                    this.operatorChoices.setChoices(
                        [{value: '', label: 'No operators available', disabled: true}],
                        'value',
                        'label',
                        true
                    );
                }
            } catch (error) {
                this.operatorChoices.setChoices(
                    [{value: '', label: 'Error loading operators', disabled: true}],
                    'value',
                    'label',
                    true
                );
            }
        }

        async loadSubtopics() {
            this.showSection('subtopicSection');
            this.subtopicSelect.innerHTML = '<option value="" selected disabled>Loading subtopics...</option>';

            try {
                const response = await fetch(`/backend/subtopic/get_by_topic_id/${this.selectedTopicId}`);
                const data = await response.json();

                this.resetSelect(this.subtopicSelect, 'Select a subtopic');
                data.data.forEach(subtopic => {
                    const option = document.createElement('option');
                    option.value = subtopic.id;
                    option.textContent = subtopic.name;
                    this.subtopicSelect.appendChild(option);
                });
            } catch (error) {
                this.subtopicSelect.innerHTML = '<option value="" selected disabled>Error loading subtopics</option>';
            }
        }


        async handleSubtopicChange() {
            this.selectedSubtopicId = this.subtopicSelect.value;
            if (!this.selectedSubtopicId) {
                this.hideSection('tertiaryTopicSection');
                return;
            }

            // Show all relevant sections
            this.showSection('tertiaryTopicSection');
            this.showSection('subjectSection');
            this.showSection('descriptionSection');
            this.showSection('prioritySection');
            this.showSection('managerSection');
            this.showSection('attachmentsSection');
            this.showSection('submitSection');

            // Set default priority to "low"
            this.priorityField.value = "low";
            this.showToast('Priority automatically set to "low"', 'info');

            // Load managers and tertiary topics
            await Promise.all([
                this.loadManagers(),
                this.loadTertiaryTopics()
            ]);
        }

        async loadTertiaryTopics() {
            this.tertiaryTopicSelect.innerHTML = '<option value="" selected disabled>Loading tertiary topics...</option>';

            try {
                const response = await fetch(`/backend/tertiary/tertiary_topic_by_subtopic_id/${this.selectedSubtopicId}`);
                const data = await response.json();

                this.resetSelect(this.tertiaryTopicSelect, 'Select a tertiary topic (optional)');
                if (data.data.length > 0) {
                    data.data.forEach(topic => {
                        const option = document.createElement('option');
                        option.value = topic.id;
                        option.textContent = topic.name;
                        this.tertiaryTopicSelect.appendChild(option);
                    });
                } else {
                    this.tertiaryTopicSelect.innerHTML = '<option value="" selected disabled>No tertiary topics available</option>';
                }
            } catch (error) {
                this.tertiaryTopicSelect.innerHTML = '<option value="" selected disabled>Error loading tertiary topics</option>';
            }
        }

        handleTertiaryTopicChange() {
            if (this.tertiaryTopicSelect.value) {
                this.showSection('subjectSection');
            } else {
                this.hideSection('subjectSection');
                this.hideSection('descriptionSection');
                this.hideSection('prioritySection');
                this.hideSection('managerSection');
                this.hideSection('attachmentsSection');
                this.hideSection('submitSection');
            }
        }

        handleSubjectInput() {
            if (this.subjectField.value.trim() !== '') {
                this.showSection('descriptionSection');
            } else {
                this.hideSection('descriptionSection');
                this.hideSection('prioritySection');
                this.hideSection('managerSection');
                this.hideSection('attachmentsSection');
                this.hideSection('submitSection');
            }
        }

        handleDescriptionInput() {
            if (this.descriptionField.value.trim() !== '') {
                this.showSection('prioritySection');

                if (!this.priorityField.value) {
                    this.priorityField.value = "low";
                    this.showToast('Priority automatically set to "low"', 'info');
                    this.loadManagers();
                    this.showSection('managerSection');
                }

                this.showSection('attachmentsSection');
                this.showSection('submitSection');
            } else {
                this.hideSection('prioritySection');
                this.hideSection('managerSection');
                this.hideSection('attachmentsSection');
                this.hideSection('submitSection');
            }
        }

        handlePriorityChange() {
            if (this.priorityField.value) {
                this.isCriticalPriority = this.priorityField.value === 'critical';
                this.showSection('managerSection');
                this.loadManagers();
                this.autoSelectChannel();
            } else {
                this.hideSection('managerSection');
            }
        }

        async loadManagers() {
            this.managerSelect.innerHTML = '<option value="" selected disabled>Loading managers...</option>';

            try {
                const response = await fetch('/backend/user/active_manager');
                const data = await response.json();

                this.resetSelect(this.managerSelect, 'Select a manager');

                if (data.data?.length > 0) {
                    const sortedManagers = data.data.sort((a, b) => b.favorite_count - a.favorite_count);

                    sortedManagers.forEach(manager => {
                        const option = document.createElement('option');
                        option.value = manager.id;
                        option.textContent = `${manager.name} ${manager.favorite_count > 0 ? `(${manager.favorite_count} similar tickets)` : ''}`;
                        option.dataset.favorites = manager.favorite_count;
                        this.managerSelect.appendChild(option);
                    });
                } else {
                    this.managerSelect.innerHTML = '<option value="" selected disabled>No managers available</option>';
                }
            } catch (error) {
                this.managerSelect.innerHTML = '<option value="" selected disabled>Error loading managers</option>';
            }
        }

        autoSelectChannel() {
            this.selectedChannels = ['mail', 'whatsapp', 'database'];
            if (this.isCriticalPriority) {
                this.selectedChannels.push('sms');
            }
            this.updateChannelSelectionUI();
            this.updateChannelSelectionSummary();
        }



        // Utility methods
        showSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section) {
                section.classList.remove('hidden-section');
                section.classList.add('visible-section');
                // Ensure Select2 is visible
                const select2 = section.querySelector('.select2-container');
                if (select2) {
                    select2.style.display = '';
                }
            }
        }

        hideSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section) {
                section.classList.remove('visible-section');
                section.classList.add('hidden-section');
            }
        }

        resetSelect(selectElement, placeholder = 'Select an option') {
            selectElement.innerHTML = `<option value="" selected disabled>${placeholder}</option>`;
        }

        showLoading(element) {
            const el = element.jquery ? element[0] : element;
            if (!el || !el.parentNode) return;

            const spinnerId = `${el.id}-spinner`;
            if (document.getElementById(spinnerId)) return;

            const spinner = document.createElement('span');
            spinner.className = 'loading-spinner ms-2';
            spinner.id = spinnerId;
            el.parentNode.insertBefore(spinner, el.nextSibling);
        }

        hideLoading(element) {
            const el = element.jquery ? element[0] : element;
            if (!el) return;

            const spinner = document.getElementById(`${el.id}-spinner`);
            spinner?.remove();
        }

        showToast(message, type = 'info') {
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

        updateChannelSelectionUI() {
            $('.channel-card').each((_, card) => {
                const channel = $(card).data('channel');
                $(card).toggleClass('border border-primary', this.selectedChannels.includes(channel));
            });

            $('#notification_channels_wrapper').html(
                this.selectedChannels.map(channel =>
                    `<input type="hidden" name="notification_channels[]" value="${channel}">`
                ).join('')
            );
        }

        updateChannelSelectionSummary() {
            const summaryContainer = document.getElementById('channelSelectionSummary') ||
                this.createChannelSummaryContainer();

            if (this.selectedChannels.length > 0) {
                summaryContainer.innerHTML = this.createChannelBadges();
                this.setupEditChannelHandler();
            } else {
                summaryContainer.innerHTML = '<small class="text-muted">No notification channels selected</small>';
            }
        }

        createChannelSummaryContainer() {
            const summaryDiv = document.createElement('div');
            summaryDiv.id = 'channelSelectionSummary';
            summaryDiv.className = 'mt-2 channel-summary';
            document.getElementById('managerSection').appendChild(summaryDiv);
            return summaryDiv;
        }

        createChannelBadges() {
            const channelBadges = this.selectedChannels.map(channel =>
                `<span class="badge bg-primary me-1">${channel}</span>`
            ).join('');

            return `
                <small class="text-muted">Notification channels:</small>
                <div>${channelBadges}</div>
                <small class="text-muted click-to-edit" style="cursor: pointer; color: #0d6efd !important;">
                  <i class="bi bi-pencil-square"></i> Click to edit
                </small>
              `;
        }

        setupEditChannelHandler() {
            $('.click-to-edit').off('click').on('click', () => {
                $('#channelModal').modal('show');
            });
        }

        handleFileSelect(event) {
            const newFiles = Array.from(event.target.files);
            this.currentFiles = [...this.currentFiles, ...newFiles.filter(newFile =>
                !this.currentFiles.some(
                    file => file.name === newFile.name &&
                        file.size === newFile.size &&
                        file.lastModified === newFile.lastModified
                )
            )];

            this.updateFileInput();
            this.updateFileDisplay();
        }

        updateFileInput() {
            const dataTransfer = new DataTransfer();
            this.currentFiles.forEach(file => dataTransfer.items.add(file));
            this.attachmentsInput.files = dataTransfer.files;
        }

        updateFileDisplay() {
            this.updateFileCount();
            this.renderFilePreviews();
        }

        updateFileCount() {
            this.fileCountDisplay.textContent = this.currentFiles.length;
        }

        renderFilePreviews() {
            this.attachmentPreviews.innerHTML = this.currentFiles.length === 0
                ? '<p class="text-muted">No files selected</p>'
                : this.currentFiles.map((file, index) => this.createFilePreview(file, index)).join('');
        }

        createFilePreview(file, index) {
            const previewDiv = document.createElement('div');
            previewDiv.className = 'd-inline-block position-relative me-2 mb-2 border p-2 rounded';
            previewDiv.dataset.fileIndex = index;

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail';
                    img.style.maxWidth = '100px';
                    img.style.maxHeight = '100px';
                    previewDiv.appendChild(img);
                    this.addFileInfo(previewDiv, file);
                    this.addRemoveButton(previewDiv, index);
                };
                reader.readAsDataURL(file);
            } else {
                const icon = document.createElement('i');
                icon.className = `bi ${this.getFileIconClass(file)}`;
                icon.style.fontSize = '2rem';
                previewDiv.appendChild(icon);
                this.addFileInfo(previewDiv, file);
                this.addRemoveButton(previewDiv, index);
            }

            return previewDiv;
        }

        addFileInfo(container, file) {
            const fileInfo = document.createElement('div');
            fileInfo.className = 'small mt-1';
            fileInfo.innerHTML = `
                <strong class="d-block text-truncate" style="max-width: 120px">${file.name}</strong>
                <small>${this.formatFileSize(file.size)}</small>
              `;
            container.appendChild(fileInfo);
        }

        getFileIconClass(file) {
            if (file.type.startsWith('image/')) return 'bi-file-image text-primary';

            const ext = file.name.split('.').pop().toLowerCase();
            switch (ext) {
                case 'pdf': return 'bi-file-earmark-pdf text-danger';
                case 'doc':
                case 'docx': return 'bi-file-earmark-word text-primary';
                case 'xls':
                case 'xlsx': return 'bi-file-earmark-excel text-success';
                default: return 'bi-file-earmark';
            }
        }

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        addRemoveButton(container, fileIndex) {
            const removeBtn = document.createElement('button');
            removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 p-0 rounded-circle';
            removeBtn.style.width = '20px';
            removeBtn.style.height = '20px';
            removeBtn.style.transform = 'translate(30%, -30%)';
            removeBtn.innerHTML = '<i class="bi bi-x" style="font-size: 0.75rem;"></i>';
            removeBtn.onclick = (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.currentFiles.splice(fileIndex, 1);
                this.updateFileInput();
                this.updateFileDisplay();
            };
            container.appendChild(removeBtn);
        }

        handleAddMoreFiles() {
            const tempInput = document.createElement('input');
            tempInput.type = 'file';
            tempInput.multiple = true;
            tempInput.accept = '.jpg,.jpeg,.png,.pdf,.doc,.docx';

            tempInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    const event = new Event('change');
                    Object.defineProperty(event, 'target', {
                        value: { files: e.target.files },
                        enumerable: true
                    });
                    this.attachmentsInput.dispatchEvent(event);
                }
            });

            tempInput.click();
        }

        async handleFormSubmit(event) {
            event.preventDefault();

            if (!this.validateForm()) {
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');

            submitBtn.disabled = true;
            submitText.textContent = 'Creating Ticket...';
            submitSpinner.style.display = 'inline-block';

            try {
                const formData = this.prepareFormData();
                const response = await fetch('/backend/ticket/store', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Network response was not ok');
                }

                if (data.success) {
                    this.showToast(data.message || 'Ticket created successfully!', 'success');
                    this.resetForm();
                } else {
                    throw new Error(data.message || 'Failed to create ticket');
                }
            } catch (error) {
                this.handleSubmitError(error);
            } finally {
                submitBtn.disabled = false;
                submitText.textContent = 'Create Ticket';
                submitSpinner.style.display = 'none';
            }
        }

        prepareFormData() {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            // Add required fields
            formData.append('saas_app_id', this.serviceSelect.value);
            formData.append('client_id', this.clientSelect.value);
            formData.append('topic_id', this.topicSelect.value);
            formData.append('sub_topic_id', this.subtopicSelect.value);
            formData.append('priority', this.priorityField.value);
            formData.append('assigned_to', this.managerSelect.value);

            // Add optional fields
            if (this.tertiaryTopicSelect.value) {
                formData.append('tertiary_topic_id', this.tertiaryTopicSelect.value);
            }

            // Add notification channels
            this.selectedChannels.forEach((channel, index) => {
                formData.append(`notification_channels[${index}]`, channel);
            });

            // Add files
            Array.from(this.attachmentsInput.files).forEach((file, i) => {
                formData.append('attachments[]', file);
            });

            return formData;
        }

        handleSubmitError(error) {
            const errors = error.message.split('<br>');
            if (errors.length > 1) {
                errors.forEach(err => this.showToast(err, 'error'));
            } else {
                this.showToast(error.message || 'Error creating ticket', 'error');
            }
        }

        validateForm() {
            const selectedServiceText = this.serviceSelect.options[this.serviceSelect.selectedIndex].text;
            const isWorkingCustomer = selectedServiceText.toLowerCase() === 'working customer';
            const customerNameInput = document.getElementById('customerNameInput');

            if (isWorkingCustomer && (!customerNameInput || !customerNameInput.value.trim())) {
                this.showToast('Please enter customer full name', 'error');
                return false;
            }

            const requiredFields = [
                { element: this.serviceSelect, name: 'Service' },
                { element: isWorkingCustomer ? customerNameInput : this.clientSelect,
                    name: isWorkingCustomer ? 'Customer Name' : 'Client' },
                { element: this.topicSelect, name: 'Topic' },
                { element: this.subtopicSelect, name: 'Subtopic' },
                { element: this.priorityField, name: 'Priority' },
                { element: this.managerSelect, name: 'Manager' }
            ];

            for (const field of requiredFields) {
                if (!field.element || !field.element.value) {
                    this.showToast(`Please ${field.name === 'Customer Name' ? 'enter' : 'select'} a ${field.name}`, 'error');
                    if (field.element) field.element.focus();
                    return false;
                }
            }

            return true;
        }

        resetForm() {
            this.form.reset();
            this.currentFiles = [];

            // Reset all selects
            this.resetSelect(this.serviceSelect, 'Select a Saas App');
            this.resetSelect(this.clientSelect, 'Select a client');
            this.resetSelect(this.topicSelect, 'Select a topic');
            this.resetSelect(this.subtopicSelect, 'Select a subtopic');
            this.resetSelect(this.tertiaryTopicSelect, 'Select a tertiary topic (optional)');
            this.resetSelect(this.managerSelect, 'Select a manager');

            // Reset priority
            this.priorityField.value = "";
            $(this.priorityField).trigger('change');

            // Hide all sections except service
            this.hideDependentSections();

            // Clear previews
            this.updateFileDisplay();
        }

        // Date/time formatting utilities
        formatTicketDate(dateString) {
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

        getTimeBadgeClass(dateString) {
            if (!dateString) return 'bg-dark';

            const now = new Date();
            const ticketDate = new Date(dateString);
            const diffInDays = Math.abs(now - ticketDate) / (24 * 60 * 60 * 1000);

            if (diffInDays < 1) return 'bg-danger';
            if (diffInDays < 7) return 'bg-primary';
            return 'bg-dark';
        }

        isTicketRecent(dateString) {
            if (!dateString) return false;
            const now = new Date();
            const ticketDate = new Date(dateString);
            const diffInHours = Math.abs(now - ticketDate) / 36e5;
            return diffInHours < 24;
        }

        timeAgo(dateString) {
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
    }


document.addEventListener('DOMContentLoaded', () => {
    // Initialize the application
    new TicketForm();
});
