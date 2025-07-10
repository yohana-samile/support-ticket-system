<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="title">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $ticket->title ?? '') }}" required>
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
    </div>
</div>


{{-- Submit/Cancel Buttons --}}
<div class="form-group mt-4 text-right">
    <a href="{{ route('frontend.ticket.index') }}" class="btn btn-dark">
        <i class="fas fa-times mr-1"></i> Cancel
    </a>

    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save mr-1"></i> {{ isset($ticket) ? 'Update Ticket' : 'Create Ticket' }}
    </button>
</div>
