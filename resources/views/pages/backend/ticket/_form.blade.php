
<div class="row">
    {{-- Left column --}}
    <div class="col-md-6">
        {{-- Title --}}
        <div class="form-group">
            <label for="title">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title"
                   class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title', $ticket->title ?? '') }}" required>
            @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        {{-- Description --}}
        <div class="form-group">
            <label for="description">Description <span class="text-danger">*</span></label>
            <textarea name="description" id="description" rows="8"
                      class="form-control @error('description') is-invalid @enderror"
                      required>{{ old('description', $ticket->description ?? '') }}</textarea>
            @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        {{-- Attachments --}}
        <div class="form-group">
            <label for="attachments">Add Attachments</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input @error('attachments.*') is-invalid @enderror" name="attachments[]" id="attachments" multiple>
                <label class="custom-file-label" for="attachments">Choose files (max 2MB each)</label>
            </div>
            @error('attachments.*') <span class="invalid-feedback">{{ $message }}</span> @enderror
            <small class="form-text text-muted">Supported: JPG, PNG, PDF, DOC, DOCX</small>
        </div>
        @if(isset($ticket) && $ticket->attachments->count() > 0)
            <div class="form-group">
                <label>Existing Attachments</label>
                <div class="row">
                    @foreach($ticket->attachments as $attachment)
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-2 d-flex align-items-center">
                                    <div class="mr-2">
                                        @if(in_array($attachment->mime_type, ['image/jpeg','image/png','image/gif']))
                                            <a href="{{ route('backend.attachment.view', $attachment->uid) }}" target="_blank">
                                                <img src="{{ route('backend.attachment.view', $attachment->uid) }}" class="img-thumbnail" style="max-height:50px;">
                                            </a>
                                        @else
                                            <i class="fas fa-file-alt fa-2x text-secondary"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <small class="d-block text-truncate" style="max-width:150px;">{{ $attachment->original_name }}</small>
                                        <div class="d-flex mt-1">
                                            <a href="{{ route('backend.attachment.download', $attachment->uid) }}" class="btn btn-sm btn-outline-primary mr-1" title="Download">
                                                <i class="fas fa-download fa-xs"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-attachment" data-attachment-id="{{ $attachment->uid }}">
                                                <i class="fas fa-trash fa-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    {{-- Right column --}}
    <div class="col-md-6">
        {{-- Status --}}
        <div class="form-group">
            <label for="status">Status <span class="text-danger">*</span></label>
            <select name="status" id="status"
                    class="form-control select2 @error('status') is-invalid @enderror" required>
                @foreach($statuses as $status)
                    <option value="{{ $status->name }}"
                        {{ old('status', $ticket->status->name ?? '') === $status->name ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                @endforeach
            </select>
            @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        {{-- Category --}}
        <div class="form-group">
            <label for="category_id">Category <span class="text-danger">*</span></label>
            <select name="category_id" id="category_id"
                    class="form-control select2 @error('category_id') is-invalid @enderror" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id', $ticket->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        {{-- Priority --}}
        <div class="form-group">
            <label for="priority">Priority <span class="text-danger">*</span></label>
            <select name="priority" id="priority" class="form-control select2 @error('priority') is-invalid @enderror" required>
                @foreach($priorities as $priority)
                    <option value="{{ $priority->name }}"
                        {{ old('priority', $ticket->priority->name ?? '') === $priority->name ? 'selected' : '' }}>
                        {{ $priority->name }}
                    </option>
                @endforeach
            </select>
            @error('priority') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        {{-- Due Date --}}
        <div class="form-group">
            <label for="due_date">Due Date</label>
            <input type="datetime-local" name="due_date" id="due_date"
                   class="form-control @error('due_date') is-invalid @enderror"
                   value="{{ old('due_date', optional($ticket->due_date ?? null)->format('Y-m-d\TH:i')) }}">
            @error('due_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        {{-- Assignee --}}
        <div class="form-group">
            <label for="assigned_to">Assign To <span class="text-danger">*</span></label>
            <select name="assigned_to" id="assigned_to" class="form-control select2 @error('assigned_to') is-invalid @enderror">
                <option value="">Unassigned</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}"
                        {{ old('assigned_to', $ticket->assigned_to ?? '') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
            @error('assigned_to') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        {{-- Reported Customer --}}
        <div class="form-group">
            <label for="reported_customer">Reported Instead Of?</label>
            <select name="reported_customer" id="reported_customer"
                    class="form-control select2 @error('reported_customer') is-invalid @enderror">
                <option value="">Select Customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->name }}"
                        {{ old('reported_customer', $ticket->reported_customer ?? '') == $customer->name ? 'selected' : '' }}>
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
            @error('reported_customer') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
</div>


{{-- Submit/Cancel Buttons --}}
<div class="form-group mt-4 text-right">
    <a href="{{ route('backend.ticket.index') }}" class="btn btn-dark">
        <i class="fas fa-times mr-1"></i> Cancel
    </a>

    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save mr-1"></i> {{ isset($ticket) ? 'Update Ticket' : 'Create Ticket' }}
    </button>
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

                    @php
                        $channels = [
                            ['name' => 'mail', 'icon' => 'fas fa-envelope', 'color' => 'primary'],
                            ['name' => 'database', 'icon' => 'fas fa-database', 'color' => 'info'],
                            ['name' => 'sms', 'icon' => 'fas fa-sms', 'color' => 'success'],
                            ['name' => 'whatsapp', 'icon' => 'fab fa-whatsapp', 'color' => 'success'],
                        ];
                    @endphp

                    @foreach($channels as $channel)
                        <div class="col-md-3">
                            <div class="card channel-card shadow-sm" data-channel="{{ $channel['name'] }}">
                                <div class="card-body">
                                    <i class="{{ $channel['icon'] }} fa-2x text-{{ $channel['color'] }} mb-2"></i>
                                    <h6>{{ ucfirst($channel['name']) }}</h6>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="notification_channels_wrapper"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Skip</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .custom-file-label::after {
            content: "Browse";
        }
        .select2-container--default .select2-selection--single {
            height: calc(1.5em + 0.75rem + 2px);
            border: 1px solid #d1d3e2;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(1.5em + 0.75rem + 2px);
        }
        .gap-3 {
            gap: 1rem;
        }

        .channel-card {
            cursor: pointer;
            transition: transform 0.2s;
        }
        .channel-card:hover {
            transform: scale(1.05);
        }
        .channel-card.border-primary {
            background-color: #f0f8ff;
        }
    </style>
@endpush


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.getElementById('description').style.height = document.getElementById('description').scrollHeight + 'px';
        document.addEventListener('DOMContentLoaded', function() {
            $(document).ready(function () {
                $('#assigned_to').on('change', function () {
                    const selectedUser = $(this).val();
                    if (selectedUser) {
                        $('#channelModal').modal('show');
                    }
                });

                let selectedChannels = [];
                $('.channel-card').on('click', function () {
                    const channel = $(this).data('channel');
                    $(this).toggleClass('border border-primary');

                    if (selectedChannels.includes(channel)) {
                        selectedChannels = selectedChannels.filter(c => c !== channel);
                    } else {
                        selectedChannels.push(channel);
                    }
                    $('#notification_channels_wrapper').html('');
                    selectedChannels.forEach(channel => {
                        $('#notification_channels_wrapper').append(`<input type="hidden" name="notification_channels[]" value="${channel}">`);
                    });
                });

                $('.select2').select2({
                    placeholder: "Select an option",
                    allowClear: true,
                    width: '100%',
                });

                $('#reported_customer').select2({
                    placeholder: "Search or add customer",
                    tags: true,
                    width: '100%',
                    allowClear: true,
                    dropdownParent: $('#reported_customer').parent(), // to avoid modal overlap
                    language: {
                        noResults: function () {
                            return `
                            <div class="d-flex justify-content-between align-items-center">
                                <span>No customer found.</span>
                                <button type="button" class="btn btn-sm btn-primary ml-2" id="addCustomerInline">
                                    <i class="fas fa-plus"></i> Add New
                                </button>
                            </div>
                        `;
                        }
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                });

                document.querySelector('.custom-file-input').addEventListener('change', function (e) {
                    var files = e.target.files;
                    var label = document.querySelector('.custom-file-label');
                    label.textContent = files.length > 1 ? files.length + ' files selected' : (files[0] ? files[0].name : 'Choose files');
                });

                if (!document.getElementById('due_date').value) {
                    const dueDateInput = document.getElementById('due_date');
                    if (dueDateInput && !dueDateInput.value) {
                        const now = new Date();
                        dueDateInput.value = now.toISOString().slice(0, 16);
                    }
                }

                $(document).on('click', '#addCustomerInline', function () {
                    const newCustomer = prompt("Enter customer name:");
                    if (newCustomer) {
                        const newOption = new Option(newCustomer, newCustomer, true, true);
                        $('#reported_customer').append(newOption).trigger('change');
                    }
                });
            });
        });
    </script>
@endpush
