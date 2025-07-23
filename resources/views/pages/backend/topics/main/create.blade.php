@extends('layouts.backend.app')
@section('title', 'Add Topic')
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add New Topic</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('backend.topic.store') }}" method="POST">
                    @csrf

                    <div class="mb-3 form-group">
                        <label for="saas_app_id">SaaS Application</label>
                        <select class="form-control select2-ajax" id="saas_app_id" name="saas_app_id" data-placeholder="Search for a SaaS app..." data-ajax-url="{{ route('backend.saas_app.search') }}">
                            <option value=""></option>
                            @if(old('saas_app_id'))
                                <option value="{{ old('saas_app_id') }}" selected>{{ old('saas_app_name') }}</option>
                            @endif
                        </select>
                        <small class="form-text text-muted">Select the SaaS application for this topic (optional)</small>
                    </div>

                    <div class="form-group">
                        <label for="name">Topic Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Submit
                    </button>
                    <a href="{{ route('backend.topic.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container {
            z-index: 1051 !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
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
        });
    </script>
@endpush
