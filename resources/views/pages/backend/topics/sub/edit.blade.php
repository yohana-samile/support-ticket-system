@extends('layouts.backend.app')
@section('title', __('Edit Sub Topic'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{__('label.subtopic_information')}}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('backend.subtopic.update', $subtopic->uid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="saas_app_id" class="form-label">{{__('label.topic')}} <span class="text-danger">*</span></label>
                                <select class="form-control select2-ajax" id="saas_app_id" name="topic_id"
                                        data-placeholder="Search main topic..."
                                        data-ajax-url="{{ route('backend.topic.search') }}">
                                    <option value=""></option>
                                    @if($subtopic->topic_id || old('topic_id'))
                                        <option value="{{ old('topic_id', $subtopic->topic_id) }}" selected>
                                            {{ old('topic_id', $subtopic->topic->name ?? '') }}
                                        </option>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="name" class="form-label">{{__('label.subtopic_name')}} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $subtopic->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="description" class="form-label">{{__('label.description')}}</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $subtopic->description) }}</textarea>
                            </div>

                            <div class="form-group">
                                <div class="">{{__('label.status')}}</div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active"
                                           name="is_active" value="1" {{ $subtopic->is_active ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">
                                        {{ $subtopic->is_active ? 'Active' : 'Inactive' }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{__('label.update_topic')}}
                                </button>
                                <a href="{{ route('backend.subtopic.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> {{__('label.cancel')}}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{__('label.subtopic_details')}}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>{{__('label.topic')}}:</strong>
                            <span class="text-muted">{{ $subtopic->topic->name ?? 'Not assigned' }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>{{__('created_at')}}:</strong>
                            <span class="text-muted">{{ $subtopic->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>{{__('label.updated_at')}}:</strong>
                            <span class="text-muted">{{ $subtopic->updated_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>{{__('label.current_status')}}:</strong>
                            <span class="badge badge-{{ $subtopic->is_active ? 'success' : 'danger' }}">
                                {{ $subtopic->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container {
            z-index: 1051 !important; /* Ensure it appears above modals */
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 with AJAX search
            $('.select2-ajax').select2({
                ajax: {
                    url: $('.select2-ajax').data('ajax-url'),
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.data.map(item => ({
                                id: item.id,
                                text: item.name
                            })),
                            pagination: {
                                more: data.next_page_url
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: $('.select2-ajax').data('placeholder'),
                allowClear: true,
                escapeMarkup: function(markup) { return markup; }
            });

            // Toggle status label when switch is clicked
            document.getElementById('is_active').addEventListener('change', function() {
                const label = document.querySelector('label[for="is_active"]');
                label.textContent = this.checked ? 'Active' : 'Inactive';
            });
        });
    </script>
@endpush
