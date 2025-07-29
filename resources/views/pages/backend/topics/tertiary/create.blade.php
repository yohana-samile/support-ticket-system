@extends('layouts.backend.app')
@section('title', __('label.add_tertiary_topic'))
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{__('label.add_tertiary_topic')}}</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('backend.tertiary.store') }}" method="POST">
                    @csrf

                    <div class="mb-3 form-group">
                        <label for="sub_topic_id">{{__('label.subtopic')}} <span class="text-danger">*</span></label>
                        <select class="form-control select2-ajax" id="sub_topic_id" name="sub_topic_id"
                                data-placeholder="{{ __('Search for a sub Topic...') }}"
                                data-ajax-url="{{ route('backend.subtopic.search') }}" required>
                            <option value=""></option>
                        </select>
                    </div>

                    <div class="mb-3 form-group">
                        <label for="topic_id">{{__('label.main_topic')}} <span class="text-danger">*</span></label>
                        <select class="form-control select2-ajax" id="topic_id" name="topic_id"
                                data-placeholder="{{ __('Search for a Main Topic...') }}"
                                data-ajax-url="{{ route('backend.topic.search') }}" required>
                            <option value=""></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="name">{{__('label.tertiary_topic_name')}} <span class="text-danger">*</span></label>
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
                    <a href="{{ route('backend.tertiary.index') }}" class="btn btn-secondary">
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
            function initSelect2(element) {
                return $(element).select2({
                    ajax: {
                        url: $(element).data('ajax-url'),
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
                    placeholder: $(element).data('placeholder'),
                    allowClear: true
                });
            }

            // Initialize both select2 inputs
            const subtopicSelect = initSelect2('#sub_topic_id');
            const topicSelect = initSelect2('#topic_id');

            // When subtopic is selected, load its main topic
            $('#sub_topic_id').on('change', function() {
                const subtopicId = $(this).val();
                if (!subtopicId) {
                    $('#topic_id').val(null).trigger('change');
                    return;
                }
                // Clear any existing options
                $('#topic_id').empty().trigger('change');

                // Add loading state
                $('#topic_id').prop('disabled', true);

                fetch("{{ route('backend.subtopic.show_for_tertiary_topic', ['subtopicId' => 'SUBTOPIC_ID']) }}".replace('SUBTOPIC_ID', subtopicId))
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.message || 'Request failed');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.data.topic) {
                            const newOption = new Option(
                                data.data.topic.name,
                                data.data.topic.id,
                                true,
                                true
                            );
                            $('#topic_id').append(newOption).trigger('change');
                        }
                    })
                    .catch(error => {
                        toastr.error('Failed to load topic information');
                    })
                    .finally(() => {
                        $('#topic_id').prop('disabled', false);
                    });
            });

            // Allow manual topic change by user
            $('#topic_id').on('change', function() {
                // Just track that the user has manually changed the topic
                $(this).data('user-changed', true);
            });
        });
    </script>
@endpush
