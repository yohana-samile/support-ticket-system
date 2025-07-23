@extends('layouts.backend.app')
@section('title', "{{__('label.add_sub_topic')}}")
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{__('label.add_sub_topic')}}</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('backend.subtopic.store') }}" method="POST">
                    @csrf

                    <div class="mb-3 form-group">
                        <label for="topic_id">{{__('label.topic')}} <span class="text-danger">*</span></label>
                        <select class="form-control select2-ajax" id="topic_id" name="topic_id" data-placeholder="Search for a Topic..." data-ajax-url="{{ route('backend.topic.search') }}">
                            <option value=""></option>
{{--                            @if(old('topic_id'))--}}
{{--                                <option value="{{ old('topic_id') }}" selected>{{ old('topic_id') }}</option>--}}
{{--                            @endif--}}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="name">{{__('label.sub_topic_name')}} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">{{__('description')}}</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                {{__('active')}}
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{__('submit')}}
                    </button>
                    <a href="{{ route('backend.subtopic.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> {{__('cancel')}}
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
