document.addEventListener('DOMContentLoaded', function() {
    const operatorChoices = new Choices('#operator', {
        removeItemButton: true,
        placeholder: true,
        searchEnabled: true,
        shouldSort: false,
        duplicateItemsAllowed: false
    });
    const form = document.getElementById('ticketForm');
    const serviceSelect = document.getElementById('service');
    const clientSelect = document.getElementById('client');
    const topicSelect = document.getElementById('topic');
    const subtopicSelect = document.getElementById('subtopic');
    const tertiaryTopicSelect = document.getElementById('tertiaryTopic');
    const managerSelect = document.getElementById('manager');
    const ticketHistoryDiv = document.getElementById('ticketHistory');
    const attachmentsInput = document.getElementById('attachments');
    const attachmentPreviews = document.getElementById('attachmentPreviews');
    const senderIdSelect = document.getElementById('senderId');
    const operatorSelect = document.getElementById('operator');
    const paymentChannelSelect = document.getElementById('paymentChannel');
    const subjectField = document.getElementById('subject');
    const descriptionField = document.getElementById('description');
    const priorityField = document.getElementById('priority');
    let selectedChannels = [];
    let isCriticalPriority = false;

    // State to track selected values
    let selectedServiceId = null;
    let selectedClientId = null;
    let selectedTopicId = null;
    let selectedSubtopicId = null;

    const urlParams = new URLSearchParams(window.location.search);
    const preSelectedServiceId = urlParams.get('saas_app_id');
    if (preSelectedServiceId) {
        // Trigger the service change handler immediately
        serviceSelect.value = preSelectedServiceId;
        handleServiceChange();
        $(serviceSelect).trigger('change');
    }

    // Initialize the form
    loadServices(preSelectedServiceId);

    function autoSelectChannel() {
        selectedChannels = [];
        $('.channel-card').removeClass('border border-primary');

        // Always select email and WhatsApp
        selectedChannels.push('mail', 'whatsapp', 'database');

        // Add SMS for critical priority
        if (isCriticalPriority) {
            selectedChannels.push('sms');
        }

        // Update UI to reflect selections
        updateChannelSelectionUI();
        updateChannelSelectionSummary();
    }

    function updateChannelSelectionUI() {
        $('.channel-card').each(function() {
            const channel = $(this).data('channel');
            if (selectedChannels.includes(channel)) {
                $(this).addClass('border border-primary');
            } else {
                $(this).removeClass('border border-primary');
            }
        });

        // Update hidden inputs
        $('#notification_channels_wrapper').html('');
        selectedChannels.forEach(channel => {
            $('#notification_channels_wrapper').append(
                `<input type="hidden" name="notification_channels[]" value="${channel}">`
            );
        });
    }

    // Update the channel card click handler to allow user modifications
    $('.channel-card').on('click', function() {
        const channel = $(this).data('channel');

        // Don't allow removing email for critical tickets
        if (isCriticalPriority && channel === 'sms') {
            toastr.warning('SMS is required for critical priority tickets');
            return;
        }

        // Toggle selection
        if (selectedChannels.includes(channel)) {
            selectedChannels = selectedChannels.filter(c => c !== channel);
        } else {
            selectedChannels.push(channel);
        }

        // Update UI
        $(this).toggleClass('border border-primary');
        updateChannelSelectionSummary();

        // Update hidden inputs
        $('#notification_channels_wrapper').html('');
        selectedChannels.forEach(channel => {
            $('#notification_channels_wrapper').append(
                `<input type="hidden" name="notification_channels[]" value="${channel}">`
            );
        });
    });

    managerSelect.addEventListener('change', function() {
        if (this.value) {
            autoSelectChannel();
        } else {
            resetChannelSelections();
        }
    });

    // Event listeners
    serviceSelect.addEventListener('change', handleServiceChange);
    clientSelect.addEventListener('change', handleClientChange);
    topicSelect.addEventListener('change', handleTopicChange);
    subtopicSelect.addEventListener('change', handleSubtopicChange);
    form.addEventListener('submit', handleFormSubmit);
    attachmentsInput.addEventListener('change', handleFileSelect);
    tertiaryTopicSelect.addEventListener('change', handleTertiaryTopicChange);
    subjectField.addEventListener('input', handleSubjectInput);
    descriptionField.addEventListener('input', handleDescriptionInput);
    priorityField.addEventListener('change', handlePriorityChange);

    function showChannelModal() {
        $('#channelModal').modal('show');
    }
    function resetChannelSelections() {
        selectedChannels = [];
        isCriticalPriority = false;
        $('.channel-card').removeClass('border border-primary');
        $('#notification_channels_wrapper').html('');
        updateChannelSelectionSummary();
    }
    function updateChannelSelectionSummary() {
        const summaryContainer = document.getElementById('channelSelectionSummary');
        if (!summaryContainer) {
            const managerSection = document.getElementById('managerSection');
            const summaryDiv = document.createElement('div');
            summaryDiv.id = 'channelSelectionSummary';
            summaryDiv.className = 'mt-2 channel-summary';
            managerSection.appendChild(summaryDiv);
        }

        const summaryElement = document.getElementById('channelSelectionSummary');
        if (selectedChannels.length > 0) {
            const channelBadges = selectedChannels.map(channel =>
                `<span class="badge bg-primary me-1">${channel}</span>`
            ).join('');
            summaryElement.innerHTML = `
                <small class="text-muted">Notification channels:</small>
                <div>${channelBadges}</div>
                <small class="text-muted click-to-edit" style="cursor: pointer; color: #0d6efd !important;">
                    <i class="bi bi-pencil-square"></i> Click to edit
                </small>
            `;

            // Add click handler to show modal for editing
            $('.click-to-edit').on('click', function() {
                $('#channelModal').modal('show');
            });
        } else {
            summaryElement.innerHTML = `
            <small class="text-muted">No notification channels selected</small>
        `;
        }
    }

    $('#channelModal .btn-primary').on('click', function() {
        $('#channelModal').modal('hide');
    });

    // Helper functions for section visibility
    function showSection(sectionId) {
        const section = document.getElementById(sectionId);
        section.classList.remove('hidden-section');
        section.classList.add('visible-section');
    }

    function hideSection(sectionId) {
        const section = document.getElementById(sectionId);
        section.classList.remove('visible-section');
        section.classList.add('hidden-section');
    }

    function resetSelect(selectElement, placeholder = 'Select an option') {
        selectElement.innerHTML = `<option value="" selected disabled>${placeholder}</option>`;
    }

    // Load services
    function loadServices(preSelectedId = null) {
        const serviceSelect = $('#service');
        const serviceSelectElement = serviceSelect[0];
        showLoading(serviceSelectElement);

        // Destroy existing Select2 instance if it exists
        if (serviceSelect.hasClass('select2-hidden-accessible')) {
            serviceSelect.select2('destroy');
        }

        // Clear any existing options
        serviceSelect.empty().append('<option value=""></option>');

        // Initialize Select2
        serviceSelect.select2({
            ajax: {
                url: serviceSelect.data('ajax-url'),
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1,
                        id: preSelectedId // Include the preselected ID in requests
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    hideLoading(serviceSelectElement);
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
            placeholder: serviceSelect.data('placeholder'),
            allowClear: true,
            escapeMarkup: function(markup) { return markup; }
        }).on('select2:select', function(e) {
            handleServiceChange();
        });

        // Handle preselected service
        if (preSelectedId) {
            // console.log("preSelectedServiceId ", preSelectedId);

            // First try to find if the option already exists
            if (serviceSelect.find(`option[value="${preSelectedId}"]`).length) {
                serviceSelect.val(preSelectedId).trigger('change');
                handleServiceChange();
                hideLoading(serviceSelectElement);
            } else {
                // Fetch the specific service
                $.ajax({
                    url: serviceSelect.data('ajax-url'),
                    data: {
                        search: '',
                        id: preSelectedId,
                        specific: true // Add this to ensure you get exactly what you requested
                    },
                    dataType: 'json'
                }).done(function(data) {
                    if (data.data && data.data.length) {
                        const service = data.data.find(item => item.id == preSelectedId);

                        if (!service) {
                            // console.error('Requested service not found in response', {
                            //     requestedId: preSelectedId,
                            //     returnedData: data.data
                            // });
                            hideLoading(serviceSelectElement);
                            return;
                        }

                        // console.log("Creating option for service:", service);
                        const option = new Option(service.name, service.id, true, true);
                        serviceSelect.append(option).trigger('change');
                        handleServiceChange();
                    }
                    hideLoading(serviceSelectElement);
                }).fail(function(error) {
                    // console.error('Failed to load preselected service:', error);
                    hideLoading(serviceSelectElement);
                });
            }
        } else {
            setTimeout(() => {
                hideLoading(serviceSelectElement);
            }, 500);
        }
    }

    // When service is selected, load clients
    function handleServiceChange() {
        selectedServiceId = serviceSelect.value;
        if (!selectedServiceId) return;

        const selectedServiceText = serviceSelect.options[serviceSelect.selectedIndex].text;

        if (selectedServiceText.toLowerCase() === 'working customer') {
            // Create or show customer name input field
            let customerNameInput = document.getElementById('customerNameInput');
            if (!customerNameInput) {
                customerNameInput = document.createElement('input');
                customerNameInput.id = 'customerNameInput';
                customerNameInput.type = 'text';
                customerNameInput.className = 'form-control mt-2';
                customerNameInput.placeholder = 'Enter customer full name';
                customerNameInput.required = true;
                clientSelect.parentNode.insertBefore(customerNameInput, clientSelect.nextSibling);

                // Add event listener for customer name input
                customerNameInput.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        // Show topic section when customer name is entered
                        showSection('topicSection');
                        topicSelect.innerHTML = '<option value="" selected disabled>Loading topics...</option>';

                        // Load topics for the selected service
                        fetch(`/backend/topic/get_by_service/${selectedServiceId}`)
                            .then(response => response.json())
                            .then(data => {
                                resetSelect(topicSelect, 'Select a topic');
                                data.data.forEach(topic => {
                                    const option = document.createElement('option');
                                    option.value = topic.id;
                                    option.textContent = topic.name;
                                    topicSelect.appendChild(option);
                                });
                            })
                            .catch(error => {
                                topicSelect.innerHTML = '<option value="" selected disabled>Error loading topics</option>';
                            });
                    } else {
                        // Hide topic section if customer name is cleared
                        hideSection('topicSection');
                    }
                });
            }
            // Hide the client select dropdown
            clientSelect.style.display = 'none';
            customerNameInput.style.display = 'block';
        } else {
            // Show client section with normal behavior
            showSection('clientSection');
            // Hide customer name input if it exists
            const customerNameInput = document.getElementById('customerNameInput');
            if (customerNameInput) {
                customerNameInput.style.display = 'none';
            }
            // Show client select dropdown
            clientSelect.style.display = 'block';
            clientSelect.innerHTML = '<option value="" selected disabled>Loading clients...</option>';
        }

        // Hide dependent sections
        hideSection('topicSection');
        hideSection('subtopicSection');
        hideSection('tertiaryTopicSection');
        hideSection('managerSection');
        hideSection('ticketHistorySection');
        hideSection('senderIdSection');
        hideSection('operatorSection');
        hideSection('paymentChannelSection');
        hideSection('subjectSection');
        hideSection('descriptionSection');
        hideSection('prioritySection');
        hideSection('attachmentsSection');
        hideSection('submitSection');

        const urlParams = new URLSearchParams(window.location.search);
        const preSelectedClientId = urlParams.get('client_id');

        fetch(`/backend/client/client_by_services/${selectedServiceId}`)
            .then(response => response.json())
            .then(data => {
                resetSelect(clientSelect, 'Select a client');
                data.data.forEach(client => {
                    const option = document.createElement('option');
                    option.value = client.id;
                    option.textContent = client.name;

                    if (preSelectedClientId && client.id == preSelectedClientId) {
                        option.selected = true;
                        selectedClientId = client.id;

                        setTimeout(() => {
                            handleClientChange();
                            $(clientSelect).trigger('change');
                        }, 100);
                    }
                    clientSelect.appendChild(option);
                });
            })
            .catch(error => {
                clientSelect.innerHTML = '<option value="" selected disabled>Error loading clients</option>';
            });
    }

    // When client is selected, load ticket history and topics
    function handleClientChange() {
        selectedClientId = clientSelect.value;
        if (!selectedClientId) return;

        showLoading(clientSelect);
        showSection('topicSection');
        topicSelect.innerHTML = '<option value="" selected disabled>Loading topics...</option>';

        const ticketHistoryDiv = document.getElementById('ticketHistory');
        ticketHistoryDiv.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 mb-0">Loading ticket history...</p>
            </div>
        `;

        fetch(`/backend/ticket/client_ticket_history/${selectedClientId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(responseData => {
                showSection('ticketHistorySection');

                if (!responseData || !responseData.data || !responseData.data.data || !Array.isArray(responseData.data.data)) {
                    throw new Error('Invalid data format received from server');
                }

                const tickets = responseData.data.data;
                const paginationInfo = responseData.data;
                const clientName = tickets[0]?.client?.name || 'Client';

                if (tickets.length === 0) {
                    ticketHistoryDiv.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-inbox empty-state-icon"></i>
                        <p class="empty-state-text">No previous tickets found for this client.</p>
                    </div>
                `;
                } else {
                    ticketHistoryDiv.innerHTML = `
                        <div class="ticket-history-header">
                            <h3 class="ticket-history-title">
                                <span class="badge bg-primary client-badge">${clientName}</span>
                                Recent Tickets
                            </h3>
                            <span class="ticket-history-count">${Math.min(tickets.length, 5)} of ${paginationInfo.total}</span>
                        </div>
                        <div id="ticketHistoryList"></div>
                    `;

                    const list = document.getElementById('ticketHistoryList');
                    if (!list) {
                        console.error('ticketHistoryList not found in DOM');
                        return;
                    }
                    const ticketsToShow = tickets.slice(0, 5);

                    ticketsToShow.forEach(ticket => {
                        const priorityClass = ticket.priority?.toLowerCase() || 'low';
                        const statusClass = ticket.status?.toLowerCase() || 'open';
                        const timeAgoResult = ticket.created_at ? timeAgo(ticket.created_at) : '';
                        const timeBadgeClass = getTimeBadgeClass(ticket.created_at);

                        let categoryPath = ticket.topic?.name || 'No topic';
                        if (ticket.subtopic?.name) categoryPath += ` › ${ticket.subtopic.name}`;
                        if (ticket.tertiary_topic?.name) categoryPath += ` › ${ticket.tertiary_topic.name}`;

                        const item = document.createElement('div');
                        item.className = `ticket-item ${isTicketRecent(ticket.created_at) ? 'recent-ticket' : ''}`;

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
                                    <i class="bi bi-calendar"></i> ${formatTicketDate(ticket.created_at)}
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
                        list.appendChild(item);
                    });

                    if (tickets.length > 5) {
                        const firstTicketUid = tickets[0]?.uid || '';
                        const viewAll = document.createElement('a');
                        viewAll.href = `/backend/ticket/client_ticket_history/${selectedClientId}`;
                        viewAll.className = 'view-all-tickets';
                        viewAll.innerHTML = 'View all tickets <i class="bi bi-chevron-right"></i>';
                        list.appendChild(viewAll);
                    }
                }
            })
            .catch(error => {
                ticketHistoryDiv.innerHTML = `
                    <div class="alert alert-danger m-3">
                        <i class="bi bi-exclamation-triangle-fill"></i> Failed to load ticket history: ${error.message}
                    </div>
                `;
                hideSection('ticketHistorySection');
            });

        fetch(`/backend/topic/get_by_service/${selectedServiceId}`)
            .then(response => response.json())
            .then(data => {
                resetSelect(topicSelect, 'Select a topic');
                data.data.forEach(topic => {
                    const option = document.createElement('option');
                    option.value = topic.id;
                    option.textContent = topic.name;
                    topicSelect.appendChild(option);
                });
                hideLoading(clientSelect);
            })
            .catch(error => {
                topicSelect.innerHTML = '<option value="" selected disabled>Error loading topics</option>';
                hideLoading(clientSelect);
            });
    }

    function formatTicketDate(dateString) {
        if (!dateString) return 'Unknown date';

        const now = new Date();
        const ticketDate = new Date(dateString);
        const diffInHours = Math.abs(now - ticketDate) / 36e5;

        if (ticketDate.toDateString() === now.toDateString()) {
            // Today
            if (diffInHours < 1) {
                const mins = Math.floor(diffInHours * 60);
                return `<span class="date-today recent-highlight">${mins}m ago</span>`;
            }
            return `<span class="date-today">Today at ${ticketDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>`;
        }

        // Yesterday
        const yesterday = new Date(now);
        yesterday.setDate(yesterday.getDate() - 1);
        if (ticketDate.toDateString() === yesterday.toDateString()) {
            return `<span class="date-yesterday">Yesterday at ${ticketDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>`;
        }

        // Older than yesterday
        return ticketDate.toLocaleDateString([], {month: 'short', day: 'numeric', year: 'numeric'});
    }

    function getTimeBadgeClass(dateString) {
        if (!dateString) return 'bg-dark';

        const now = new Date();
        const ticketDate = new Date(dateString);
        const diffInDays = Math.abs(now - ticketDate) / (24 * 60 * 60 * 1000);

        if (diffInDays < 1) {
            return 'bg-danger'; // Less than 1 day - red
        } else if (diffInDays < 7) {
            return 'bg-primary'; // 1-7 days - blue
        } else {
            return 'bg-dark'; // More than 7 days - dark
        }
    }

    function isTicketRecent(dateString) {
        if (!dateString) return false;
        const now = new Date();
        const ticketDate = new Date(dateString);
        const diffInHours = Math.abs(now - ticketDate) / 36e5;
        return diffInHours < 24; // Consider recent if within last 24 hours
    }

    function timeAgo(dateString) {
        if (!dateString) return '';

        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        if (seconds < 60) {
            return `${seconds} second${seconds === 1 ? '' : 's'} ago`;
        }

        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) {
            return `${minutes} minute${minutes === 1 ? '' : 's'} ago`;
        }

        const hours = Math.floor(minutes / 60);
        if (hours < 24) {
            return `${hours} hour${hours === 1 ? '' : 's'} ago`;
        }

        const days = Math.floor(hours / 24);
        if (days < 7) {
            return `${days} day${days === 1 ? '' : 's'} ago`;
        }

        const weeks = Math.floor(days / 7);
        if (weeks < 4) {
            return `${weeks} week${weeks === 1 ? '' : 's'} ago`;
        }

        const months = Math.floor(days / 30);
        if (months < 12) {
            return `${months} month${months === 1 ? '' : 's'} ago`;
        }

        const years = Math.floor(days / 365);
        return `${years} year${years === 1 ? '' : 's'} ago`;
    }

    // When topic is selected, load subtopics or special fields
    function handleTopicChange() {
        selectedTopicId = topicSelect.value;
        if (!selectedTopicId) return;

        const selectedTopicText = topicSelect.options[topicSelect.selectedIndex].text.toLowerCase();
        const isPaymentTopic = selectedTopicText.includes('payment') || selectedTopicText.includes('billing') || selectedTopicText.includes('invoice');
        const isSmsTopic = selectedTopicText.includes('sms') || selectedTopicText.includes('message') || selectedTopicText.includes('delivery');

        hideSection('subtopicSection');
        hideSection('tertiaryTopicSection');
        hideSection('managerSection');
        hideSection('senderIdSection');
        hideSection('operatorSection');
        hideSection('paymentChannelSection');
        hideSection('subjectSection');
        hideSection('descriptionSection');
        hideSection('prioritySection');
        hideSection('attachmentsSection');
        hideSection('submitSection');
        hideSection('dateSection');

        if (isSmsTopic) {
            showSection('senderIdSection');
            senderIdSelect.innerHTML = '<option value="" selected disabled>Loading sender IDs...</option>';

            const selectedClientId = clientSelect.value;

            if (!selectedClientId) {
                senderIdSelect.innerHTML = '<option value="" selected disabled>Please select a client first</option>';
                return;
            }

            fetch(`/backend/sender_id/active_sender_ids/${selectedClientId}`)
                .then(response => response.json())
                .then(data => {
                    resetSelect(senderIdSelect, 'Select a sender ID');
                    data.data.forEach(sender => {
                        const option = document.createElement('option');
                        option.value = sender.id;
                        option.textContent = `${sender.sender_id}`;
                        option.dataset.operators = JSON.stringify(sender.operators || []);
                        senderIdSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    senderIdSelect.innerHTML = '<option value="" selected disabled>Error loading sender IDs</option>';
                });

            senderIdSelect.addEventListener('change', handleSenderIdChange);

            // Show date section for SMS issues
            showDateSection('When did the SMS fail to deliver?');
        }
        else if (isPaymentTopic) {
            showSection('paymentChannelSection');
            paymentChannelSelect.innerHTML = '<option value="" selected disabled>Loading payment channels...</option>';

            fetch('/backend/payment_channel/active_payment_channels')
                .then(response => response.json())
                .then(data => {
                    resetSelect(paymentChannelSelect, 'Select a payment channel');
                    data.data.forEach(channel => {
                        const option = document.createElement('option');
                        option.value = channel.id;
                        option.textContent = channel.name;
                        paymentChannelSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    paymentChannelSelect.innerHTML = '<option value="" selected disabled>Error loading channels</option>';
                });

            paymentChannelSelect.addEventListener('change', function() {
                if (this.value) {
                    loadSubtopics();
                }
            });

            // Show date section for payment issues
            showDateSection('When was the payment made?');
        }
        else {
            loadSubtopics();
        }
    }

    function showDateSection(labelText) {
        // Create or update the date section
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
            // Insert after the topic section
            document.getElementById('topicSection').after(dateSection);
        } else {
            // Update the label if it already exists
            dateSection.querySelector('label').textContent = labelText;
        }

        showSection('dateSection');
    }

    function handleSenderIdChange() {
        if (!this.value) {
            hideSection('operatorSection');
            hideSection('subtopicSection');
            return;
        }

        showSection('operatorSection');

        // Clear existing choices
        operatorChoices.clearStore();
        operatorChoices.setChoices([{value: '', label: 'Loading operators...', disabled: true}], 'value', 'label', true);

        fetch('/backend/operator/get_all_operator')
            .then(response => response.json())
            .then(data => {
                if (data.data && data.data.length > 0) {
                    operatorChoices.setChoices(
                        data.data.map(operator => ({
                            value: operator.id,
                            label: operator.name
                        })),
                        'value',
                        'label',
                        true
                    );

                    // Add event listener for operator selection changes
                    operatorChoices.passedElement.element.addEventListener('change', function() {
                        // Check if at least one operator is selected
                        const selectedOperators = operatorChoices.getValue(true);
                        if (selectedOperators && selectedOperators.length > 0) {
                            loadSubtopics();
                        } else {
                            hideSection('subtopicSection');
                        }
                    }, false);
                } else {
                    operatorChoices.setChoices([{value: '', label: 'No operators available', disabled: true}], 'value', 'label', true);
                }
            })
            .catch(error => {
                operatorChoices.setChoices([{value: '', label: 'Error loading operators', disabled: true}], 'value', 'label', true);
            });
    }

    function loadSubtopics() {
        showSection('subtopicSection');
        subtopicSelect.innerHTML = '<option value="" selected disabled>Loading subtopics...</option>';

        fetch(`/backend/subtopic/get_by_topic_id/${selectedTopicId}`)
            .then(response => response.json())
            .then(data => {
                resetSelect(subtopicSelect, 'Select a subtopic');
                data.data.forEach(subtopic => {
                    const option = document.createElement('option');
                    option.value = subtopic.id;
                    option.textContent = subtopic.name;
                    subtopicSelect.appendChild(option);
                });
            })
            .catch(error => {
                subtopicSelect.innerHTML = '<option value="" selected disabled>Error loading subtopics</option>';
            });
    }

    // When subtopic is selected, load tertiary topics
    function handleSubtopicChange() {
        selectedSubtopicId = subtopicSelect.value;
        if (!selectedSubtopicId) {
            hideSection('tertiaryTopicSection');
            return;
        }

        // Show tertiary topic section
        showSection('tertiaryTopicSection');
        tertiaryTopicSelect.innerHTML = '<option value="" selected disabled>Loading tertiary topics...</option>';

        // Load tertiary topics
        fetch(`/backend/tertiary/tertiary_topic_by_subtopic_id/${selectedSubtopicId}`)
            .then(response => response.json())
            .then(data => {
                resetSelect(tertiaryTopicSelect, 'Select a tertiary topic (optional)');
                if (data.data.length > 0) {
                    data.data.forEach(tertiaryTopic => {
                        const option = document.createElement('option');
                        option.value = tertiaryTopic.id;
                        option.textContent = tertiaryTopic.name;
                        tertiaryTopicSelect.appendChild(option);
                    });
                } else {
                    tertiaryTopicSelect.innerHTML = '<option value="" selected disabled>No tertiary topics available</option>';
                }
            })
            .catch(error => {
                tertiaryTopicSelect.innerHTML = '<option value="" selected disabled>Error loading tertiary topics</option>';
            });
    }

    // Handle tertiary topic selection
    function handleTertiaryTopicChange() {
        if (this.value) {
            // Show subject field when tertiary topic is selected
            showSection('subjectSection');
        } else {
            // Hide all subsequent fields if tertiary topic is cleared
            hideSection('subjectSection');
            hideSection('descriptionSection');
            hideSection('prioritySection');
            hideSection('managerSection');
            hideSection('attachmentsSection');
            hideSection('submitSection');
        }
    }

    // Handle subject input
    function handleSubjectInput() {
        if (this.value.trim() !== '') {
            // Show description and priority fields when subject is filled
            showSection('descriptionSection');
            //showSection('prioritySection');
        } else {
            // Hide all subsequent fields if subject is cleared
            hideSection('descriptionSection');
            hideSection('prioritySection');
            hideSection('managerSection');
            hideSection('attachmentsSection');
            hideSection('submitSection');
        }
    }

    // Handle description input
    function handleDescriptionInput() {
        if (this.value.trim() !== '') {
            // Show attachments and submit button when description is filled
            showSection('prioritySection');

            if (!priorityField.value || priorityField.value === "") {
                priorityField.value = "low";
                toastr.info('Priority automatically set to "low"', '', {
                    timeOut: 3000,
                    positionClass: 'toast-bottom-right'
                });
                loadManagers();
                showSection('managerSection');
            }
            showSection('attachmentsSection');
            showSection('submitSection');
        } else {
            // Hide these fields if description is cleared
            hideSection('prioritySection');
            hideSection('managerSection');
            hideSection('attachmentsSection');
            hideSection('submitSection');
        }
    }

    // Handle priority change - NEW FUNCTION
    function handlePriorityChange() {
        if (this.value) {
            isCriticalPriority = this.value === 'critical';
            showSection('managerSection');
            loadManagers();

            //auto select channel
            autoSelectChannel();
        } else {
            hideSection('managerSection');
        }
    }

    // Load managers
    function loadManagers() {
        managerSelect.innerHTML = '<option value="" selected disabled>Loading managers...</option>';

        fetch('/backend/user/active_manager')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                resetSelect(managerSelect, 'Select a manager');

                if (data.data && data.data.length > 0) {
                    // Sort managers by favorite_count (descending)
                    const sortedManagers = data.data.sort((a, b) => b.favorite_count - a.favorite_count);

                    sortedManagers.forEach(manager => {
                        const option = document.createElement('option');
                        option.value = manager.id;
                        option.textContent = `${manager.name} ${manager.favorite_count > 0 ? `(${manager.favorite_count} similar tickets)` : ''}`;
                        option.dataset.favorites = manager.favorite_count;
                        managerSelect.appendChild(option);
                    });
                } else {
                    managerSelect.innerHTML = '<option value="" selected disabled>No managers available</option>';
                }
            })
            .catch(error => {
                managerSelect.innerHTML = '<option value="" selected disabled>Error loading managers</option>';
            });
    }

    const addMoreFilesBtn = document.getElementById('addMoreFiles');
    const fileCountDisplay = document.getElementById('fileCount');

    let currentFiles = [];
    attachmentsInput.addEventListener('change', handleFileSelect);

    function handleFileSelect(event) {
        const newFiles = Array.from(event.target.files);

        newFiles.forEach(newFile => {
            const isDuplicate = currentFiles.some(
                file => file.name === newFile.name &&
                    file.size === newFile.size &&
                    file.lastModified === newFile.lastModified
            );
            if (!isDuplicate) {
                currentFiles.push(newFile);
            }
        });

        updateFileInput();
        updateFileDisplay();
    }

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
        attachmentPreviews.innerHTML = '';

        if (currentFiles.length === 0) {
            attachmentPreviews.innerHTML = '<p class="text-muted">No files selected</p>';
            return;
        }

        currentFiles.forEach((file, index) => {
            const previewDiv = document.createElement('div');
            previewDiv.className = 'd-inline-block position-relative me-2 mb-2 border p-2 rounded';
            previewDiv.dataset.fileIndex = index;

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail';
                    img.style.maxWidth = '100px';
                    img.style.maxHeight = '100px';
                    previewDiv.appendChild(img);

                    addFileInfo(previewDiv, file);
                    addRemoveButton(previewDiv, index);
                };
                reader.readAsDataURL(file);
            } else {
                const icon = document.createElement('i');
                icon.className = 'bi ' + getFileIconClass(file);
                icon.style.fontSize = '2rem';
                previewDiv.appendChild(icon);

                addFileInfo(previewDiv, file);
                addRemoveButton(previewDiv, index);
            }

            attachmentPreviews.appendChild(previewDiv);
        });
    }

    function addFileInfo(container, file) {
        const fileInfo = document.createElement('div');
        fileInfo.className = 'small mt-1';
        fileInfo.innerHTML = `
            <strong class="d-block text-truncate" style="max-width: 120px">${file.name}</strong>
            <small>${formatFileSize(file.size)}</small>
        `;
        container.appendChild(fileInfo);
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
            default: return 'bi-file-earmark';
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function addRemoveButton(container, fileIndex) {
        const removeBtn = document.createElement('button');
        removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 p-0 rounded-circle';
        removeBtn.style.width = '20px';
        removeBtn.style.height = '20px';
        removeBtn.style.transform = 'translate(30%, -30%)';
        removeBtn.innerHTML = '<i class="bi bi-x" style="font-size: 0.75rem;"></i>';
        removeBtn.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation();

            // Remove the file from currentFiles
            currentFiles.splice(fileIndex, 1);

            // Update the UI and file input
            updateFileInput();
            updateFileDisplay();
        };
        container.appendChild(removeBtn);
    }
    addMoreFilesBtn.addEventListener('click', function() {
        const tempInput = document.createElement('input');
        tempInput.type = 'file';
        tempInput.multiple = true;
        tempInput.accept = '.jpg,.jpeg,.png,.pdf,.doc,.docx';

        tempInput.addEventListener('change', function(e) {
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

    function handleFormSubmit(event) {
        event.preventDefault();

        // Check if we're in "Working Customer" mode
        const selectedServiceText = serviceSelect.options[serviceSelect.selectedIndex].text;
        const isWorkingCustomer = selectedServiceText.toLowerCase() === 'working customer';
        const customerNameInput = document.getElementById('customerNameInput');

        if (isWorkingCustomer && (!customerNameInput || !customerNameInput.value)) {
            toastr.error('Please enter customer full name');
            return;
        }

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

        if (isWorkingCustomer) {
            formData.append('customer_name', customerNameInput.value);
        } else {
            // Normal client selection
            formData.append('client_id', clientSelect.value);
        }

        // required fields
        formData.append('saas_app_id', serviceSelect.value);
        formData.append('client_id', clientSelect.value);
        formData.append('topic_id', topicSelect.value);
        formData.append('sub_topic_id', subtopicSelect.value);
        formData.append('title', subjectField.value);
        formData.append('description', descriptionField.value);
        formData.append('priority', priorityField.value);
        formData.append('assigned_to', managerSelect.value);

        const issueDate = document.getElementById('issueDate');
        if (issueDate && issueDate.value) {
            formData.append('issue_date', issueDate.value);
        }

        if (tertiaryTopicSelect.value) {
            formData.append('tertiary_topic_id', tertiaryTopicSelect.value);
        }
        if (!document.getElementById('senderIdSection').classList.contains('hidden-section')) {
            formData.append('sender_id', senderIdSelect.value);

            const selectedOperators = Array.from(operatorSelect.selectedOptions)
                .map(option => option.value)
                .filter(value => value);

            selectedOperators.forEach(operatorId => {
                formData.append('operator[]', operatorId);
            });
        }
        if (!document.getElementById('paymentChannelSection').classList.contains('hidden-section')) {
            formData.append('payment_channel_id', paymentChannelSelect.value);
        }
        selectedChannels.forEach((channel, index) => {
            formData.append(`notification_channels[${index}]`, channel);
        });

        const files = attachmentsInput.files;
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
                    toastr.success(data.message || 'Ticket created successfully!');
                    resetForm();
                } else {
                    throw new Error(data.message || 'Failed to create ticket');
                }
            })
            .catch(error => {
                const errors = error.message.split('<br>');
                if (errors.length > 1) {
                    errors.forEach(err => toastr.error(err));
                } else {
                    toastr.error(error.message || 'Error creating ticket');
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitText.textContent = 'Create Ticket';
                submitSpinner.style.display = 'none';
            });
    }

    function resetForm() {
        form.reset();

        const customerNameInput = document.getElementById('customerNameInput');
        if (customerNameInput) {
            customerNameInput.value = '';
            customerNameInput.style.display = 'none';
        }
        clientSelect.style.display = 'block';

        // Reset all selects
        resetSelect(serviceSelect, 'Select a Saas App');
        resetSelect(clientSelect, 'Select a client');
        resetSelect(topicSelect, 'Select a topic');
        resetSelect(subtopicSelect, 'Select a subtopic');
        resetSelect(tertiaryTopicSelect, 'Select a tertiary topic (optional)');
        resetSelect(managerSelect, 'Select a manager');
        resetSelect(senderIdSelect, 'Select sender ID');
        resetSelect(operatorSelect, 'Select mobile operator');
        resetSelect(paymentChannelSelect, 'Select a payment channel');

        // Reset priority to Low
        priorityField.value = ""; //"low"
        $(priorityField).trigger('change');

        // Hide all sections except service
        hideSection('clientSection');
        hideSection('topicSection');
        hideSection('subtopicSection');
        hideSection('tertiaryTopicSection');
        hideSection('managerSection');
        hideSection('ticketHistorySection');
        hideSection('senderIdSection');
        hideSection('operatorSection');
        hideSection('paymentChannelSection');
        hideSection('subjectSection');
        hideSection('descriptionSection');
        hideSection('prioritySection');
        hideSection('attachmentsSection');
        hideSection('submitSection');

        // Clear previews
        currentFiles = [];
        updateFileInput();
        updateFileDisplay();
    }

    function validateForm() {
        const selectedServiceText = serviceSelect.options[serviceSelect.selectedIndex].text;
        const isWorkingCustomer = selectedServiceText.toLowerCase() === 'working customer';
        const customerNameInput = document.getElementById('customerNameInput');

        if (isWorkingCustomer && (!customerNameInput || !customerNameInput.value.trim())) {
            toastr.error('Please enter customer full name');
            return false;
        }

        const requiredFields = [
            { element: serviceSelect, name: 'Service' },
            { element: isWorkingCustomer ? customerNameInput : clientSelect, name: isWorkingCustomer ? 'Customer Name' : 'Client' },
            { element: topicSelect, name: 'Topic' },
            { element: subtopicSelect, name: 'Subtopic' },
            { element: subjectField, name: 'Subject' },
            { element: descriptionField, name: 'Description' },
            { element: priorityField, name: 'Priority' },
            { element: managerSelect, name: 'Manager' }
        ];

        for (const field of requiredFields) {
            if (!field.element || !field.element.value) {
                toastr.error(`Please ${field.name === 'Customer Name' ? 'enter' : 'select'} a ${field.name}`);
                if (field.element) field.element.focus();
                return false;
            }
        }

        return true;
    }

    function showLoading(element) {
        const el = element.jquery ? element[0] : element;

        if (!el || !el.parentNode) {
            console.error('Invalid element passed to showLoading');
            return;
        }

        const spinnerId = `${el.id}-spinner`;
        if (document.getElementById(spinnerId)) {
            return;
        }

        const spinner = document.createElement('span');
        spinner.className = 'loading-spinner ms-2';
        spinner.id = spinnerId;
        el.parentNode.insertBefore(spinner, el.nextSibling);
    }

    function hideLoading(element) {
        const el = element.jquery ? element[0] : element;
        if (!el) return;

        const spinnerId = `${el.id}-spinner`;
        const spinner = document.getElementById(spinnerId);
        if (spinner) {
            spinner.remove();
        }
    }
});
