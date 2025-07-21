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

    managerSelect.addEventListener('change', function() {
        if (this.value) {
            resetChannelSelections();
            showChannelModal();
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
            `;
        } else {
            summaryElement.innerHTML = `
                <small class="text-muted">No notification channels selected</small>
            `;
        }
    }
    $('.channel-card').on('click', function() {
        const channel = $(this).data('channel');
        $(this).toggleClass('border border-primary');

        if (selectedChannels.includes(channel)) {
            selectedChannels = selectedChannels.filter(c => c !== channel);
        } else {
            selectedChannels.push(channel);
        }

        // Update hidden inputs and summary
        $('#notification_channels_wrapper').html('');
        selectedChannels.forEach(channel => {
            $('#notification_channels_wrapper').append(
                `<input type="hidden" name="notification_channels[]" value="${channel}">`
            );
        });
    });
    $('#channelModal .btn-primary').on('click', function() {
        updateChannelSelectionSummary();
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
        showLoading(serviceSelect);

        fetch('/backend/saas_app/saas_app')
            .then(response => response.json())
            .then(data => {
                serviceSelect.innerHTML = '<option value="" selected disabled>Select a Saas App</option>';
                data.data.forEach(service => {
                    const option = document.createElement('option');
                    option.value = service.id;
                    option.textContent = service.name;

                    // Mark as selected if it matches the pre-selected ID
                    if (preSelectedId && service.id == preSelectedId) {
                        option.selected = true;
                        selectedServiceId = service.id; // Update the state
                    }

                    serviceSelect.appendChild(option);
                });
                hideLoading(serviceSelect);

                if (preSelectedId && selectedServiceId) {
                    handleServiceChange();
                    $(serviceSelect).trigger('change');
                }
            })
            .catch(error => {
                hideLoading(serviceSelect);
            });
    }

    // When service is selected, load clients
    function handleServiceChange() {
        selectedServiceId = serviceSelect.value;
        if (!selectedServiceId) return;

        // Show client section
        showSection('clientSection');
        clientSelect.innerHTML = '<option value="" selected disabled>Loading clients...</option>';

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

        fetch(`/backend/client/client_by_services/${selectedServiceId}`)
            .then(response => response.json())
            .then(data => {
                resetSelect(clientSelect, 'Select a client');
                data.data.forEach(client => {
                    const option = document.createElement('option');
                    option.value = client.id;
                    option.textContent = client.name;
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

                        let categoryPath = ticket.topic?.name || 'No topic';
                        if (ticket.subtopic?.name) categoryPath += ` › ${ticket.subtopic.name}`;
                        if (ticket.tertiary_topic?.name) categoryPath += ` › ${ticket.tertiary_topic.name}`;

                        const item = document.createElement('div');
                        item.className = 'ticket-item';
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
                                <i class="bi bi-calendar"></i> ${ticket.created_at ? new Date(ticket.created_at).toLocaleDateString() : 'Unknown date'}
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
                            <span class="ticket-time">
                                <i class="bi bi-clock"></i> ${ticket.created_at ? timeAgo(ticket.created_at) : ''}
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

        fetch(`/backend/topic/topic_by_services/${selectedServiceId}`)
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

    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        let interval = Math.floor(seconds / 31536000);
        if (interval >= 1) return interval + "y";

        interval = Math.floor(seconds / 2592000);
        if (interval >= 1) return interval + "mo";

        interval = Math.floor(seconds / 86400);
        if (interval >= 1) return interval + "d";

        interval = Math.floor(seconds / 3600);
        if (interval >= 1) return interval + "h";

        interval = Math.floor(seconds / 60);
        if (interval >= 1) return interval + "m";

        return Math.floor(seconds) + "s";
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
        }
        else {
            loadSubtopics();
        }
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
            showSection('prioritySection');
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
            showSection('attachmentsSection');
            showSection('submitSection');
        } else {
            // Hide these fields if description is cleared
            hideSection('attachmentsSection');
            hideSection('submitSection');
        }
    }

    // Handle priority change - NEW FUNCTION
    function handlePriorityChange() {
        if (this.value) {
            showSection('managerSection');
            loadManagers();
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

        // required fields
        formData.append('saas_app_id', serviceSelect.value);
        formData.append('client_id', clientSelect.value);
        formData.append('topic_id', topicSelect.value);
        formData.append('sub_topic_id', subtopicSelect.value);
        formData.append('title', subjectField.value);
        formData.append('description', descriptionField.value);
        formData.append('priority', priorityField.value);
        formData.append('assigned_to', managerSelect.value);

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
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Network response was not ok');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Ticket created successfully!');
                    resetForm();
                } else {
                    throw new Error(data.message || 'Failed to create ticket');
                }
            })
            .catch(error => {
                alert(`Error creating ticket: ${error.message}`);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitText.textContent = 'Create Ticket';
                submitSpinner.style.display = 'none';
            });
    }

    function resetForm() {
        form.reset();

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
        const requiredFields = [
            { element: serviceSelect, name: 'Service' },
            { element: clientSelect, name: 'Client' },
            { element: topicSelect, name: 'Topic' },
            { element: subtopicSelect, name: 'Subtopic' },
            { element: subjectField, name: 'Subject' },
            { element: descriptionField, name: 'Description' },
            { element: priorityField, name: 'Priority' },
            { element: managerSelect, name: 'Manager' }
        ];

        for (const field of requiredFields) {
            if (!field.element.value) {
                alert(`Please select a ${field.name}`);
                field.element.focus();
                return false;
            }
        }

        return true;
    }

    function showLoading(element) {
        const spinner = document.createElement('span');
        spinner.className = 'loading-spinner ms-2';
        spinner.id = `${element.id}-spinner`;
        element.parentNode.insertBefore(spinner, element.nextSibling);
    }

    function hideLoading(element) {
        const spinner = document.getElementById(`${element.id}-spinner`);
        if (spinner) spinner.remove();
    }
});
