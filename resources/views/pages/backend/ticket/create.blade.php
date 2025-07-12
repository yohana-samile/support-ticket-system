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
    </style>
</head>
<body>
<div class="container py-5">
    <h1 class="mb-4">Create New Ticket</h1>

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

        <!-- Ticket History (display only) -->
        <div class="mb-3 form-section hidden-section" id="ticketHistorySection">
            <label class="form-label">Client's Previous Tickets</label>
            <div class="card">
                <div class="card-body">
                    <div id="ticketHistory"></div>
                </div>
            </div>
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

        <!-- Attachments -->
        <div class="mb-3 form-section hidden-section" id="attachmentsSection">
            <label for="attachments" class="form-label">Attachments</label>
            <input class="form-control" type="file" id="attachments" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
            <div class="form-text">Max file size: 2MB. Allowed types: JPG, PNG, PDF, DOC</div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('asset/js/script.js') }}"></script>
</body>
</html>
