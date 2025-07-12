document.addEventListener('DOMContentLoaded', function() {
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

    // Initialize the form
    loadServices();

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
    function loadServices() {
        showLoading(serviceSelect);

        fetch('/backend/saas_app/saas_app')
            .then(response => response.json())
            .then(data => {
                serviceSelect.innerHTML = '<option value="" selected disabled>Select a Saas App</option>';
                data.data.forEach(service => {
                    const option = document.createElement('option');
                    option.value = service.id;
                    option.textContent = service.name;
                    serviceSelect.appendChild(option);
                });
                hideLoading(serviceSelect);
            })
            .catch(error => {
                console.error('Error loading Saas App:', error);
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
                console.error('Error loading clients:', error);
                clientSelect.innerHTML = '<option value="" selected disabled>Error loading clients</option>';
            });
    }

    // When client is selected, load ticket history and topics
    function handleClientChange() {
        selectedClientId = clientSelect.value;
        if (!selectedClientId) return;

        // Show loading states
        showLoading(clientSelect);
        showSection('topicSection');
        topicSelect.innerHTML = '<option value="" selected disabled>Loading topics...</option>';

        // Load ticket history
        fetch(`/backend/ticket/client_ticket_history/${selectedClientId}`)
            .then(response => response.json())
            .then(data => {
                showSection('ticketHistorySection');

                if (data.data.length === 0) {
                    ticketHistoryDiv.innerHTML = '<p class="text-muted">No previous tickets found for this client.</p>';
                    document.getElementById('clientHistoryText').textContent = 'This client has no previous tickets.';
                } else {
                    ticketHistoryDiv.innerHTML = '';
                    const list = document.createElement('ul');
                    list.className = 'list-group list-group-flush';

                    data.data.slice(0, 5).forEach(ticket => {
                        const item = document.createElement('li');
                        item.className = 'list-group-item';
                        item.innerHTML = `
                            <strong>${ticket.subject}</strong> (${ticket.priority})
                            <br><small class="text-muted">${new Date(ticket.created_at).toLocaleDateString()} - ${ticket.status}</small>
                        `;
                        list.appendChild(item);
                    });

                    ticketHistoryDiv.appendChild(list);
                    document.getElementById('clientHistoryText').textContent = `Showing ${Math.min(data.data.length, 5)} of ${data.data.length} previous tickets.`;
                }
            })
            .catch(error => {
                console.error('Error loading ticket history:', error);
                hideSection('ticketHistorySection');
            });

        // Load topics for the selected service
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
                console.error('Error loading topics:', error);
                topicSelect.innerHTML = '<option value="" selected disabled>Error loading topics</option>';
                hideLoading(clientSelect);
            });
    }

    // When topic is selected, load subtopics or special fields
    function handleTopicChange() {
        selectedTopicId = topicSelect.value;
        if (!selectedTopicId) return;

        const selectedTopicText = topicSelect.options[topicSelect.selectedIndex].text.toLowerCase();
        const isPaymentTopic = selectedTopicText.includes('payment') || selectedTopicText.includes('billing') || selectedTopicText.includes('invoice');
        const isSmsTopic = selectedTopicText.includes('sms') || selectedTopicText.includes('message') || selectedTopicText.includes('delivery');

        // Hide all dependent sections first
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
            // Show sender ID selection first
            showSection('senderIdSection');
            senderIdSelect.innerHTML = '<option value="" selected disabled>Loading sender IDs...</option>';

            // Load all available sender IDs
            fetch('/backend/sender_id/active_sender_ids')
                .then(response => response.json())
                .then(data => {
                    resetSelect(senderIdSelect, 'Select a sender ID');
                    data.data.forEach(sender => {
                        const option = document.createElement('option');
                        option.value = sender.id;
                        option.textContent = `${sender.sender_id}`;
                        option.dataset.operators = JSON.stringify(sender.operators);
                        senderIdSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    senderIdSelect.innerHTML = '<option value="" selected disabled>Error loading sender IDs</option>';
                });

            // Set up sender ID change handler
            senderIdSelect.addEventListener('change', handleSenderIdChange);
        }
        else if (isPaymentTopic) {
            // Show payment channel section first
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
                    console.error('Error loading payment channels:', error);
                    paymentChannelSelect.innerHTML = '<option value="" selected disabled>Error loading channels</option>';
                });

            // Set up payment channel change handler
            paymentChannelSelect.addEventListener('change', function() {
                if (this.value) {
                    loadSubtopics();
                }
            });
        }
        else {
            // For regular topics, load subtopics directly
            loadSubtopics();
        }
    }

    function handleSenderIdChange() {
        if (!this.value) {
            hideSection('operatorSection');
            hideSection('subtopicSection');
            return;
        }

        // Show operator section
        showSection('operatorSection');
        operatorSelect.innerHTML = '<option value="" selected disabled>Loading operators...</option>';

        // Load all operators
        fetch('/backend/operator/get_all_operator')
            .then(response => response.json())
            .then(data => {
                resetSelect(operatorSelect, 'Select mobile operator');
                data.data.forEach(operator => {
                    const option = document.createElement('option');
                    option.value = operator.id;
                    option.textContent = operator.name;
                    operatorSelect.appendChild(option);
                });
            })
            .catch(error => {
                operatorSelect.innerHTML = '<option value="" selected disabled>Error loading operators</option>';
            });

        // Set up operator change handler
        operatorSelect.addEventListener('change', function() {
            if (this.value) {
                loadSubtopics();
            }
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
                console.error('Error loading managers:', error);
                managerSelect.innerHTML = '<option value="" selected disabled>Error loading managers</option>';
            });
    }

    // Handle file selection for attachments
    function handleFileSelect(event) {
        attachmentPreviews.innerHTML = '';
        const files = event.target.files;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const previewDiv = document.createElement('div');
            previewDiv.className = 'd-inline-block position-relative me-2 mb-2';

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'attachment-preview img-thumbnail';
                    img.alt = file.name;
                    previewDiv.appendChild(img);
                    addRemoveButton(previewDiv, file.name);
                };
                reader.readAsDataURL(file);
            } else {
                const icon = document.createElement('i');
                icon.className = 'bi bi-file-earmark attachment-preview';

                const ext = file.name.split('.').pop().toLowerCase();
                if (ext === 'pdf') icon.className += '-pdf text-danger';
                else if (['doc', 'docx'].includes(ext)) icon.className += '-word text-primary';
                else icon.className += '-text';

                previewDiv.appendChild(icon);
                const nameSpan = document.createElement('span');
                nameSpan.className = 'd-block small text-center';
                nameSpan.textContent = file.name.length > 15 ? file.name.substring(0, 12) + '...' : file.name;
                previewDiv.appendChild(nameSpan);
                addRemoveButton(previewDiv, file.name);
            }

            attachmentPreviews.appendChild(previewDiv);
        }
    }

    function addRemoveButton(container, fileName) {
        const removeBtn = document.createElement('button');
        removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 p-0 rounded-circle';
        removeBtn.style.width = '20px';
        removeBtn.style.height = '20px';
        removeBtn.style.transform = 'translate(30%, -30%)';
        removeBtn.innerHTML = '<i class="bi bi-x" style="font-size: 0.75rem;"></i>';
        removeBtn.onclick = (e) => {
            e.preventDefault();
            container.remove();
            // Remove the file from the input
            const dataTransfer = new DataTransfer();
            const files = attachmentsInput.files;
            for (let i = 0; i < files.length; i++) {
                if (files[i].name !== fileName) {
                    dataTransfer.items.add(files[i]);
                }
            }
            attachmentsInput.files = dataTransfer.files;
        };
        container.appendChild(removeBtn);
    }

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

        // Add all required fields
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
            formData.append('operator', operatorSelect.value); // Changed to match backend
        }
        if (!document.getElementById('paymentChannelSection').classList.contains('hidden-section')) {
            formData.append('payment_channel_id', paymentChannelSelect.value);
        }

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
                console.log('Raw response:', response);
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
        attachmentPreviews.innerHTML = '';
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
