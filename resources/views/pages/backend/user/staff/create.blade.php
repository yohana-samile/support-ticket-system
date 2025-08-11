@extends('layouts.backend.app')
@section('title', 'Add Staff')
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{__('label.add_new_staff')}}</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('backend.user.store') }}" method="POST" id="staffForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{__('name')}} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">{{__('email')}} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">{{__('phone')}} <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}" required>
                                <input type="hidden" name="phone_country" id="phone_country">
                                <small class="text-warning">Phone number must be registered on WhatsApp</small>
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="department">{{__('department')}}</label>
                                <input type="text" class="form-control" id="department"
                                       name="department" value="{{ old('department') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active"
                                   name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                {{__('Active')}}
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role_id">{{__('label.roles')}} <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="role_id" name="role_id[]"
                                        multiple="multiple" required
                                        data-placeholder="{{__('label.select_roles')}}">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}"
                                                @if(old('role_id') && in_array($role->id, old('role_id'))) selected @endif>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="topic_ids">{{__('label.topic')}}</label>
                                <select class="form-control select2-ajax" id="topic_ids" name="topic_ids[]"
                                        multiple="multiple"
                                        data-placeholder="{{__('Search for topics of specialization...')}}"
                                        data-ajax-url="{{ route('backend.topic.search') }}">
                                    @if(old('topic_ids'))
                                        @foreach(old('topic_ids') as $topic_id)
                                            <option value="{{ $topic_id }}" selected>{{ $topic_id }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{__('label.submit')}}
                        </button>
                        <a href="{{ route('backend.user.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> {{__('label.cancel')}}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css"/>
    <style>
        .select2-container {
            z-index: 1051 !important;
        }
        .iti {
            width: 100%;
        }
        .intl-tel-input {
            display: block;
        }
        .is-invalid + .intl-tel-input {
            border-color: #dc3545;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Initialize phone input
            const phoneInput = document.getElementById("phone");
            const phoneForm = $("#staffForm");
            const hiddenCountry = document.getElementById("phone_country");

            const iti = window.intlTelInput(phoneInput, {
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.min.js",
                preferredCountries: ['tz', 'us', 'gb', 'ke', 'ng', 'za', 'in'],
                separateDialCode: true,
                initialCountry: "auto",
                geoIpLookup: function(success) {
                    fetch("https://ipapi.co/json")
                        .then(res => res.json())
                        .then(data => success(data.country_code))
                        .catch(() => success("tz"));
                },
                customPlaceholder: function(selectedCountryData) {
                    return "e.g. " + " 620 350 083";
                }
            });

            // Store country code
            phoneInput.addEventListener('countrychange', function() {
                hiddenCountry.value = iti.getSelectedCountryData().iso2;
            });

            // Initialize Select2 for roles
            $('.select2').select2({
                placeholder: $(this).data('placeholder'),
                allowClear: true,
                width: '100%',
                closeOnSelect: false // Keep dropdown open for multiple selections
            });

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

                        var allOption = {
                            id: 'all',
                            text: 'All Topics'
                        };

                        return {
                            results: [allOption].concat(data.data.map(item => ({
                                id: item.id,
                                text: item.name
                            }))),
                            pagination: {
                                more: data.next_page_url
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
                placeholder: $('.select2-ajax').data('placeholder'),
                allowClear: true,
                escapeMarkup: function(markup) { return markup; },
                templateResult: function (data) {
                    if (data.loading) {
                        return data.text;
                    }
                    if (data.id === 'all') {
                        return $('<span class="text-primary font-weight-bold">' + data.text + '</span>');
                    }
                    return data.text;
                },
                templateSelection: function (data) {
                    return data.text;
                }
            });

            //handle all selection
            $('.select2-ajax').on('select2:select', function (e) {
                var data = e.params.data;
                if (data.id === 'all') {
                    $(this).val(['all']).trigger('change');
                } else {
                    var currentValues = $(this).val();
                    if (currentValues && currentValues.includes('all')) {
                        $(this).val(currentValues.filter(item => item !== 'all')).trigger('change');
                    }
                }
            })

            //prevent deselecting all when click on it
            $('.select2-ajax').on('select2:unselect', function (e) {
                if (e.params.data.id === 'all') {
                    e.preventDefault();
                }
            })

            // Form validation
            phoneForm.on("submit", function(e) {
                e.preventDefault();

                // Validate phone number
                if (phoneInput.value.trim()) {
                    if (!iti.isValidNumber()) {
                        toastMessage('error', 'Please enter a valid phone number');
                        phoneInput.focus();
                        return;
                    }
                    phoneInput.value = iti.getNumber();
                }

                // Validate roles
                if ($('#role_id').val() === null || $('#role_id').val().length === 0) {
                    toastMessage('error', 'Please select at least one role');
                    return;
                }

                let formData = phoneForm.serialize();

                $.ajax({
                    url: phoneForm.attr("action"),
                    method: "POST",
                    data: formData,
                    beforeSend: function() {
                        Swal.showLoading();
                    },
                    success: function (response) {
                        Swal.close();
                        toastMessage('success', response.message || 'User created successfully');
                        phoneForm[0].reset();
                        phoneForm.find('select').val(null).trigger('change');
                    },
                    error: function (xhr) {
                        Swal.close();
                        let errorMessage = 'Something went wrong';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastMessage('error', errorMessage);
                    }
                })

            });

            async function toastMessage(type = 'error', message) {
                toastr[type](message, '', {
                    timeOut: 3000,
                    positionClass: 'toast-top-right',
                    progressBar: true,
                    closeButton: true
                });
            }
        });
    </script>
@endpush
