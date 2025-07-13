<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Ticket</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* Add to your existing styles */
        #attachmentPreviews {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .attachment-preview-container {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 8px;
            width: 120px;
            position: relative;
        }

        .file-icon {
            font-size: 2rem;
            display: block;
            margin-bottom: 5px;
        }

        .file-info {
            font-size: 0.8rem;
            word-break: break-all;
        }

        #addMoreFiles {
            cursor: pointer;
        }

        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(0,0,0,.1);
            border-radius: 50%;
            border-top-color: #0d6efd;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .attachment-preview {
            max-width: 100px;
            max-height: 100px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .form-section {
            transition: all 0.3s ease;
        }
        .hidden-section {
            display: none;
            opacity: 0;
            height: 0;
            overflow: hidden;
        }
        .visible-section {
            display: block;
            opacity: 1;
            height: auto;
        }
        .ticket-history-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #eaeaea;
        }

        .ticket-history-header {
            background: #f8fafc;
            padding: 16px 20px;
            border-bottom: 1px solid #eaeaea;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ticket-history-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .client-badge {
            font-size: 13px;
            font-weight: 500;
            padding: 4px 10px;
            border-radius: 12px;
            background: #3b82f6;
            color: white;
        }
        .ticket-history-count {
            font-size: 13px;
            color: #64748b;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 12px;
        }

        .ticket-history-list {
            max-height: 500px;
            overflow-y: auto;
        }

        .ticket-item {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .ticket-item:hover {
            background: #f8fafc;
        }

        .ticket-item:last-child {
            border-bottom: none;
        }

        .ticket-main-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .ticket-title {
            font-size: 15px;
            font-weight: 500;
            color: #1e293b;
            margin: 0;
            flex: 1;
        }

        .ticket-status {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 12px;
            margin-left: 8px;
        }

        .ticket-status.open {
            background: #e0f2fe;
            color: #0369a1;
        }

        .ticket-status.closed {
            background: #dcfce7;
            color: #166534;
        }

        .ticket-status.pending {
            background: #fef9c3;
            color: #854d0e;
        }

        .ticket-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 8px;
        }

        .ticket-category {
            font-size: 12px;
            color: #475569;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .ticket-assignee {
            display: flex;
            align-items: center;
            font-size: 12px;
            color: #64748b;
        }

        .ticket-assignee i {
            margin-right: 4px;
        }

        .ticket-date {
            font-size: 12px;
            color: #64748b;
            display: flex;
            align-items: center;
        }

        .ticket-date i {
            margin-right: 4px;
        }

        .ticket-number {
            font-size: 12px;
            color: #64748b;
            display: flex;
            align-items: center;
        }

        .ticket-number i {
            margin-right: 4px;
        }

        .ticket-description {
            font-size: 13px;
            color: #64748b;
            line-height: 1.4;
            margin-top: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ticket-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
        }

        .ticket-priority {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 12px;
        }

        .ticket-priority.low {
            background: #ecfdf5;
            color: #059669;
        }

        .ticket-priority.medium {
            background: #fef3c7;
            color: #b45309;
        }

        .ticket-priority.high {
            background: #fee2e2;
            color: #b91c1c;
        }

        .ticket-priority.critical {
            background: #b91c1c;
            color: white;
        }

        .ticket-time {
            font-size: 11px;
            color: #94a3b8;
        }

        .view-all-tickets {
            text-align: center;
            padding: 12px;
            font-size: 14px;
            font-weight: 500;
            color: #3b82f6;
            background: #f8fafc;
            border-top: 1px solid #eaeaea;
            transition: all 0.2s ease;
        }

        .view-all-tickets:hover {
            background: #f1f5f9;
            color: #2563eb;
        }

        .empty-state {
            padding: 40px 20px;
            text-align: center;
        }

        .empty-state-icon {
            font-size: 48px;
            color: #cbd5e1;
            margin-bottom: 16px;
        }

        .empty-state-text {
            font-size: 14px;
            color: #64748b;
            margin: 0;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <h1 class="mb-4">Create New Ticket</h1>

    <div class="row">
        <div class="col-md-6">
            <form id="ticketForm" novalidate>
                @csrf

                <!-- Service Selection -->
                <div class="mb-3 form-section visible-section" id="serviceSection">
                    <label for="service" class="form-label">SaaS Service <span class="text-danger">*</span></label>
                    <select class="form-select" id="service" name="saas_app_id" required>
                        <option value="" >Select a service</option>
                    </select>
                    <div class="form-text">Select the service this ticket is about</div>
                </div>

                <!-- Client Selection -->
                <div class="mb-3 form-section hidden-section" id="clientSection">
                    <label for="client" class="form-label">Client <span class="text-danger">*</span></label>
                    <select class="form-select" id="client" name="client_id" required >
                        <option value="" >Select a client</option>
                    </select>
                    <div class="form-text" id="clientHistoryText"></div>
                </div>


                <!-- Topic Selection -->
                <div class="mb-3 form-section hidden-section" id="topicSection">
                    <label for="topic" class="form-label">Topic <span class="text-danger">*</span></label>
                    <select class="form-select" id="topic" name="topic_id" required >
                        <option value="" >Select a topic</option>
                    </select>
                </div>

                <!-- SMS Specific Fields -->
                <div class="mb-3 form-section hidden-section" id="senderIdSection">
                    <label for="senderId" class="form-label">Sender ID <span class="text-danger">*</span></label>
                    <select class="form-select" id="senderId" name="sender_id" required >
                        <option value="" >Select sender ID</option>
                    </select>
                    <div class="form-text">Select the sender ID for SMS delivery</div>
                </div>

                <div class="mb-3 form-section hidden-section" id="operatorSection">
                    <label for="operator" class="form-label">Mobile Operator <span class="text-danger">*</span></label>
                    <select class="form-select" id="operator" name="mobile_operator" required >
                        <option value="" >Select mobile operator</option>
                    </select>
                    <div class="form-text">Select the mobile network operator</div>
                </div>

                <!-- Payment Specific Fields -->
                <div class="mb-3 form-section hidden-section" id="paymentChannelSection">
                    <label for="paymentChannel" class="form-label">Payment Channel <span class="text-danger">*</span></label>
                    <select class="form-select" id="paymentChannel" name="payment_channel_id" required >
                        <option value="" >Select a payment channel</option>
                    </select>
                    <div class="form-text">Select the payment method related to this issue</div>
                </div>

                <!-- Subtopic Selection -->
                <div class="mb-3 form-section hidden-section" id="subtopicSection">
                    <label for="subtopic" class="form-label">Subtopic <span class="text-danger">*</span></label>
                    <select class="form-select" id="subtopic" name="sub_topic_id" required >
                        <option value="" >Select a subtopic</option>
                    </select>
                </div>

                <!-- Tertiary Topic Selection -->
                <div class="mb-3 form-section hidden-section" id="tertiaryTopicSection">
                    <label for="tertiaryTopic" class="form-label">Tertiary Topic</label>
                    <select class="form-select" id="tertiaryTopic" name="tertiary_topic_id" >
                        <option value="" >Select a tertiary topic (optional)</option>
                    </select>
                </div>

                <!-- Ticket Details -->
                <div class="mb-3 form-section hidden-section" id="subjectSection">
                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="subject" name="title" required>
                </div>

                <div class="mb-3 form-section hidden-section" id="descriptionSection">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" rows="4" name="description" required></textarea>
                </div>

                <div class="mb-3 form-section hidden-section" id="prioritySection">
                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                    <select class="form-select" id="priority" name="priority" required>
                        <option value="" >Select priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>

                <!-- Manager Selection -->
                <div class="mb-3 form-section hidden-section" id="managerSection">
                    <label for="manager" class="form-label">Assign To <span class="text-danger">*</span></label>
                    <select class="form-select" id="manager" name="assigned_to" required >
                        <option value="" >Select a manager</option>
                    </select>
                    <div class="form-text">Managers with experience in this topic are shown first</div>
                </div>

                <div class="mb-3 form-section hidden-section" id="attachmentsSection">
                    <label for="attachments" class="form-label">Attachments</label>
                    <div class="input-group">
                        <input class="form-control" type="file" id="attachments" name="attachments[]" multiple
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
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
                        <h3 class="ticket-history-title">
                            Client's Previous Tickets
                        </h3>
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
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('asset/js/script.js') }}"></script>
</body>
</html>
