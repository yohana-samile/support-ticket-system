
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
