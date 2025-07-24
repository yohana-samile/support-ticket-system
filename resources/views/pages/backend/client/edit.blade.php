@extends('layouts.backend.app')
@section('title', 'Edit Client')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{__('label.client_information')}}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('backend.client.update', $client->uid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="saas_app_id" class="form-label">{{__('label.saas_application')}} <span class="text-danger">*</span></label>
                                <select class="form-control select2-ajax" id="saas_app_id" name="saas_app_id"
                                        data-placeholder="Search for a SaaS app..."
                                        data-ajax-url="{{ route('backend.saas_app.search') }}" required>
                                    <option value=""></option>
                                    @if($client->saas_app_id || old('saas_app_id'))
                                        <option value="{{ old('saas_app_id', $client->saas_app_id) }}" selected>
                                            {{ old('saas_app_name', $client->saasApp->name ?? '') }}
                                        </option>
                                    @endif
                                </select>
                                <small class="form-text text-muted">Select the SaaS </small>
                            </div>

                            <div class="form-group">
                                <label for="name" class="form-label">{{__('label.name')}} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $client->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label">{{__('label.email')}} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $client->email) }}" required>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="phone" class="form-label">{{__('label.phone')}} <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $client->phone) }}" required>
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="">{{__('label.status')}}</div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active"
                                           name="is_active" value="1" {{ $client->is_active ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">
                                        {{ $client->is_active ? 'Active' : 'Inactive' }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{__('label.update_topic')}}
                                </button>
                                <a href="{{ route('backend.client.index') }}" class="btn btn-secondary">
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
                        <h6 class="m-0 font-weight-bold text-primary">{{__('label.topic_details')}}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>{{__('label.saas_application')}}:</strong>
                            <span class="text-muted">{{ $client->saasApp->name ?? 'Not assigned' }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>{{__('created_at')}}:</strong>
                            <span class="text-muted">{{ $client->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>{{__('label.updated_at')}}:</strong>
                            <span class="text-muted">{{ $client->updated_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>{{__('label.current_status')}}:</strong>
                            <span class="badge badge-{{ $client->is_active ? 'success' : 'danger' }}">
                                {{ $client->is_active ? 'Active' : 'Inactive' }}
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
