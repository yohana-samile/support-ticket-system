
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Ticket Reports') }}</h6>
                    <button id="toggleFiltersBtn" class="btn btn-sm btn-link ml-2">
                        <i class="fas fa-filter"></i> <span id="toggleFiltersText">{{ __('Hide Filters') }}</span>
                    </button>
                </div>

                <div class="d-flex ml-auto">
                    <button id="exportBtn" class="btn btn-sm btn-outline-success mr-2">
                        <i class="fas fa-file-export"></i> {{ __('label.export') }}
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="showSummaryCards()">
                        <i class="fas fa-arrow-left"></i> {{ __('label.back_to_summary') }}
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="filterSection">
                    <form id="reportFilterForm">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label>{{ __('Date Range') }}</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" name="start_date" id="startDate">
                                    <input type="date" class="form-control" name="end_date" id="endDate">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label>{{ __('Client') }}</label>
                                <select class="form-control select2-ajax" name="client_id" id="clientFilter" data-ajax-url="{{ route('backend.client.search') }}">
                                    <option value="">{{ __('All Clients') }}</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>{{ __('Assigned Staff') }}</label>
                                <select class="form-control select2" name="assigned_to" id="staffFilter">
                                    <option value="">{{ __('All Staff') }}</option>
                                    @foreach($staff as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>{{ __('label.status') }}</label>
                                <select class="form-control select2" name="status" id="statusFilter">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    @foreach($statues as $status)
                                        <option value="{{ $status->name }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label>{{ __('label.topic') }}</label>
                                <select class="form-control select2-ajax" name="topic_id" id="topicFilter" data-ajax-url="{{ route('backend.topic.search') }}">
                                    <option value="">{{ __('All Topics') }}</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>{{ __('label.subtopic') }}</label>
                                <select class="form-control select2" name="subtopic_id" id="subtopicFilter" disabled>
                                    <option value="">{{ __('Select Subtopic') }}</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>{{ __('Tertiary Topic') }}</label>
                                <select class="form-control select2" name="tertiary_topic_id" id="tertiaryTopicFilter" disabled>
                                    <option value="">{{ __('Select Tertiary Topic') }}</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>{{ __('Priority') }}</label>
                                <select class="form-control select2" name="priority" id="priorityFilter">
                                    <option value="">{{ __('All Priorities') }}</option>
                                    @foreach($priorities as $priority)
                                        <option value="{{ $priority->name }}">{{ $priority->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label>{{ __('Mobile Operator') }}</label>
                                <select class="form-control select2" name="mno" id="mnoFilter">
                                    <option value="">{{ __('All Operators') }}</option>
                                    @foreach($mnos as $mno)
                                        <option value="{{ $mno->id }}">{{ $mno->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>{{ __('Payment Channel') }}</label>
                                <select class="form-control select2" name="payment_channel" id="paymentChannelFilter">
                                    <option value="">{{ __('All Channels') }}</option>
                                    @foreach($paymentChannels as $channel)
                                        <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>{{ __('label.sender') }}</label>
                                <select class="form-control select2-ajax" name="sender_id" id="senderIdFilter" data-ajax-url="{{ route('backend.sender_id.search') }}">
                                    <option value="">{{ __('All Sender IDs') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>{{ __('label.saas_app') }}</label>
                                <select class="form-control select2-ajax" name="saas_app" id="saasAppIdFilter" data-ajax-url="{{ route('backend.saas_app.search') }}">
                                    <option value="">{{ __('label.saas_app') }}</option>
                                </select>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="button" id="filterBtn" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> {{ __('label.filter') }}
                                </button>
                                <button type="reset" id="resetBtn" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> {{ __('label.reset') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <hr>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="ticketsTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>{{ __('Ticket ID') }}</th>
                            <th>{{ __('label.saas_app') }}</th>
                            <th>{{ __('label.client') }}</th>
                            <th>{{ __('Subject') }}</th>
                            <th>{{ __('label.topic') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('Priority') }}</th>
                            <th>{{ __('Assigned To') }}</th>
                            <th>{{ __('Mobile Operator') }}</th>
                            <th>{{ __('Payment Channel') }}</th>
                            <th>{{ __('Sender ID') }}</th>
                            <th>{{ __('Created At') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    <link href="{{ asset('asset/vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        #filterSection {
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .filters-collapsed {
            max-height: 0;
            opacity: 0;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .filters-expanded {
            max-height: 1000px;
            opacity: 1;
        }
    </style>

    <script src="{{ asset('asset/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('asset/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('asset/vendor/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            let filtersVisible = false;
            $('#filterSection')
                .removeClass('filters-expanded')
                .addClass('filters-collapsed');
            $('#toggleFiltersText').text('{{ __("Show Filters") }}');

            $('#toggleFiltersBtn').click(function() {
                filtersVisible = !filtersVisible;
                if (filtersVisible) {
                    $('#filterSection').removeClass('filters-collapsed').addClass('filters-expanded');
                    $('#toggleFiltersText').text('{{ __("Hide Filters") }}');
                } else {
                    $('#filterSection').removeClass('filters-expanded').addClass('filters-collapsed');
                    $('#toggleFiltersText').text('{{ __("Show Filters") }}');
                }
            });

            $('#saasAppIdFilter').select2({
                ajax: {
                    url: $('#saasAppIdFilter').data('ajax-url'),
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
                                text: item.abbreviation
                            })),
                            pagination: {
                                more: data.next_page_url
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: "{{ __('Search Saas app...') }}",
                allowClear: true
            });


            $('#senderIdFilter').select2({
                ajax: {
                    url: $('#senderIdFilter').data('ajax-url'),
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
                                text: item.sender_id
                            })),
                            pagination: {
                                more: data.next_page_url
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                placeholder: "{{ __('Search Sender ID...') }}",
                allowClear: true
            });

            $('#clientFilter').select2({
                ajax: {
                    url: $('#clientFilter').data('ajax-url'),
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
                placeholder: $('#clientFilter').data('placeholder'),
                allowClear: true
            });


            $('#topicFilter').select2({
                ajax: {
                    url: $('#topicFilter').data('ajax-url'),
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
                placeholder: $('#topicFilter').data('placeholder'),
                allowClear: true
            });

            // Initialize DataTable
            const table = $('#ticketsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('backend.report.data') }}",
                    data: function(d) {
                        d.start_date = $('#startDate').val();
                        d.end_date = $('#endDate').val();
                        d.client_id = $('#clientFilter').val();
                        d.assigned_to = $('#staffFilter').val();
                        d.status = $('#statusFilter').val();
                        d.topic_id = $('#topicFilter').val();
                        d.subtopic_id = $('#subtopicFilter').val();
                        d.tertiary_topic_id = $('#tertiaryTopicFilter').val();
                        d.priority = $('#priorityFilter').val();
                        d.mno = $('#mnoFilter').val();
                        d.payment_channel = $('#paymentChannelFilter').val();
                        d.sender_id = $('#senderIdFilter').val();
                        d.saas_app = $('#saasAppIdFilter').val();
                    }
                },
                columns: [
                    { data: 'ticket_number', name: 'ticket_number' },
                    { data: 'saas_app.abbreviation', name: 'saas_app.abbreviation' },
                    { data: 'client.name', name: 'client.name' },
                    { data: 'title', name: 'title' },
                    {
                        data: 'topic_path',
                        name: 'topic_path',
                        render: function(data, type, row) {
                            return data ? data.replace(/->/g, ' &rarr; ') : '';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            const statusClass = {
                                'open': 'badge-primary',
                                'resolved': 'badge-success',
                                'closed': 'badge-secondary'
                            }[data] || 'badge-light';
                            return `<span class="badge ${statusClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                        }
                    },
                    {
                        data: 'priority',
                        name: 'priority',
                        render: function(data) {
                            const priorityClass = {
                                'low': 'badge-info',
                                'medium': 'badge-warning',
                                'high': 'badge-danger',
                                'critical': 'badge-dark'
                            }[data] || 'badge-light';
                            return `<span class="badge ${priorityClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                        }
                    },
                    { data: 'assigned_to.name', name: 'assigned_to.name' },
                    { data: 'mno.name', name: 'mno.name', defaultContent: '' },
                    { data: 'payment_channel.name', name: 'payment_channel.name', defaultContent: '' },
                    { data: 'sender.sender_id', name: 'sender.sender_id', defaultContent: '' },

                    {
                        data: 'created_at',
                        name: 'created_at',

                        render: function(data) {
                            return new Date(data).toLocaleString();
                        }
                    }
                ]
            });

            // Load subtopics when topic changes
            $('#topicFilter').change(function() {
                const topicId = $(this).val();
                $('#subtopicFilter').empty().append('<option value="">{{ __('Select Subtopic') }}</option>');
                $('#tertiaryTopicFilter').empty().append('<option value="">{{ __('Select Tertiary Topic') }}</option>');

                if (topicId) {
                    $('#subtopicFilter').prop('disabled', false);
                    $.get("{{ route('backend.subtopic.get_by_topic_id', ['topicId' => '__topicId__']) }}".replace('__topicId__', topicId), function(data) {
                        data.data.forEach(subtopic => {
                            $('#subtopicFilter').append(`<option value="${subtopic.id}">${subtopic.name}</option>`);
                        });
                    });
                } else {
                    $('#subtopicFilter').prop('disabled', true);
                    $('#tertiaryTopicFilter').prop('disabled', true);
                }
            });

            // Load tertiary topics when subtopic changes
            $('#subtopicFilter').change(function() {
                const subtopicId = $(this).val();
                $('#tertiaryTopicFilter').empty().append('<option value="">{{ __('Select Tertiary Topic') }}</option>');

                if (subtopicId) {
                    $('#tertiaryTopicFilter').prop('disabled', false);
                    $.get("{{ route('backend.tertiary.tertiary_topic_by_subtopic_id', ['subtopicUid' => '__subtopicId__']) }}".replace('__subtopicId__', subtopicId), function(data) {
                        data.data.forEach(tertiaryTopic => {
                            $('#tertiaryTopicFilter').append(`<option value="${tertiaryTopic.id}">${tertiaryTopic.name}</option>`);
                        });
                    });
                } else {
                    $('#tertiaryTopicFilter').prop('disabled', true);
                }
            });

            // Apply filters
            $('#filterBtn').click(function() {
                table.ajax.reload();
            });

            // Reset filters
            $('#resetBtn').click(function() {
                $('#reportFilterForm')[0].reset();
                $('.select2').val(null).trigger('change');
                $('#subtopicFilter').prop('disabled', true);
                $('#tertiaryTopicFilter').prop('disabled', true);
                table.ajax.reload();
            });

            // Export functionality
            $('#exportBtn').click(function() {
                const form = $('#reportFilterForm');
                const url = "{{ route('backend.report.export') }}";

                // Create a temporary form for download
                const tempForm = $('<form>', {
                    'action': url,
                    'method': 'POST',
                    'target': '_blank'
                }).append($('<input>', {
                    'name': '_token',
                    'value': "{{ csrf_token() }}",
                    'type': 'hidden'
                }));

                // Add all filter values to the form
                form.find('input, select').each(function() {
                    if ($(this).attr('name')) {
                        tempForm.append($('<input>', {
                            'name': $(this).attr('name'),
                            'value': $(this).val(),
                            'type': 'hidden'
                        }));
                    }
                });

                // Submit the form
                $('body').append(tempForm);
                tempForm.submit();
                tempForm.remove();
            });
        });
    </script>
