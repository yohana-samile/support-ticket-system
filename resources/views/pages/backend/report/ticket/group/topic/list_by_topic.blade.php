@extends('layouts.backend.app')
@section('title', __('label.tickets_by_topic'))
@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-light">
            <h6 class="m-0 font-weight-bold card-header-title">{{ $title }}</h6>
            <div>
                <span class="badge badge-light mr-2">
                    <i class="fas fa-ticket-alt"></i> Total: <span>{{ $total_tickets ?? 0 }}</span>
                </span>
                <button class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#reportFilterModal">
                    <i class="fas fa-filter"></i> {{ __('label.filter') }}
                </button>
                <button class="btn btn-sm btn-outline-success" id="exportData">
                    <i class="fas fa-file-export"></i> {{ __('label.export') }}
                </button>
            </div>
        </div>
        <div class="card-body">
            <select class="form-control mb-3 select2-ajax" id="topicSelector"
                    data-placeholder="Search for a Topic..."
                    data-ajax-url="{{ route('backend.topic.search') }}" style="width: 300px;">
                @if(isset($topic_selected))
                    <option value="{{ $topic_uid }}" selected>{{ $topic_selected }}</option>
                @endif
            </select>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="listTicketsByTopicTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>{{__('label.client')}}</th>
                            <th>{{__('label.subject')}}</th>
                            <th>{{__('label.topic')}}</th>
                            <th>{{__('label.subtopic')}}</th>
                            <th>{{__('label.reported_by')}}</th>
                            <th>{{__('label.priority')}}</th>
                            <th>{{__('label.status')}}</th>
                            <th>{{__('label.created_at')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('includes.partials.filter_modal')
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            function loadTickets(topic) {
                if ($.fn.DataTable.isDataTable('#listTicketsByTopicTable')) {
                    table.destroy();
                }

                table = $('#listTicketsByTopicTable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: "{{ route('backend.report.list_by_topic', '__uid__') }}".replace('__uid__', topic),
                        data: function (d) {
                            d.start_date = $('#startDate').val();
                            d.end_date = $('#endDate').val();
                        }
                    },
                    columns: [
                        { data: 'client.name', defaultContent: '' },
                        { data: 'title' },
                        { data: 'topic.name', defaultContent: '' },
                        { data: 'subtopic.name', defaultContent: '' },
                        { data: 'user.name', defaultContent: '' },
                        { data: 'priority_badge' },
                        { data: 'status_badge' },
                        { data: 'when_reported' },
                    ]
                });
            }

            let initialUid = $('#topicSelector').val();
            loadTickets(initialUid);

            $('#topicSelector').on('change', function () {
                let topic = $(this).val();
                if (topic) {
                    loadTickets(topic);
                }
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

                        return {
                            results: data.data.map(item => ({
                                id: item.uid,
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


        {{--$('#exportData').on('click', function () {--}}
        {{--    let uid = $('#topicSelector').val();--}}
        {{--    let start_date = $('#startDate').val();--}}
        {{--    let end_date = $('#endDate').val();--}}

        {{--    let exportUrl = "{{ route('backend.report.exportSummary') }}" +--}}
        {{--        '?topic_id=' + uid + '&start_date=' + start_date + '&end_date=' + end_date;--}}

        {{--    window.location.href = exportUrl;--}}
        {{--});--}}
    </script>
@endpush
