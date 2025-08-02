@extends('layouts.backend.app')
@section('title', __('label.topic'))
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
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="clientTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="20%">{{ __('label.client') }}</th>
                            <th width="10%">{{ __('Total Tickets') }}</th>
                            @foreach($statuses as $status)
                                <th width="{{ floor(70/count($statuses)) }}%">
                                       <span class="{{ $status->text_color_class }} {{ $status->color_class ?? 'secondary' }}"
                                             style="
                                                  display: inline-block;
                                                  font-size: 0.8rem;
                                                  padding: 0.35em 0.65em;
                                                  border-radius: 0.25rem;
                                                  font-weight: 600;
                                                  line-height: 1;
                                                  text-align: center;
                                                  white-space: nowrap;
                                                  vertical-align: baseline;
                                                  {{ $status->color_class ? 'background-color: ' . $status->color_class . ';' : '' }}
                                                  {{ $status->text_color_class ? 'color: ' . $status->text_color_class . ';' : '' }}
                                              ">
                                            {{ ucfirst($status->name) }}
                                       </span>
                                </th>
                            @endforeach
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
    <style>
        #clientTable tbody tr:hover {
            background-color: #f5f5f5;
            cursor: pointer;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            table = $('#clientTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('backend.report.client_data') }}",
                    data: function (d) {
                        d.start_date = $('#startDate').val();
                        d.end_date = $('#endDate').val();
                    }
                },
                columns: [
                    {
                        data: 'name',
                        name: 'name', // This is the only searchable column
                        orderable: true,
                        render: function (data, type, row) {
                            return `<strong>${row.name}</strong>`
                        }
                    },
                    {
                        data: 'tickets_count',
                        name: null,
                        searchable: false,
                        orderable: false,
                        className: 'font-weight-bold'
                    },
                    @foreach($statuses as $status)
                        {
                            data: '{{ $status->slug }}_tickets_count',
                            name: null,
                            searchable: false,
                            orderable: false,
                            className: function (row, type, val, meta) {
                                return val > 0 ? 'text-{{ $status->color_class ?? "primary" }}' : '';
                            }
                        },
                    @endforeach
                ],
                dom: '<"top"lf>rt<"bottom"ip>',
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search client...",
                },
            });

            // Click event for rows
            $('#clientTable tbody').on('click', 'tr', function () {
                const data = table.row(this).data();
                if (data && data.uid) {
                    window.location.href = "{{ route('backend.report.list_by_client', '__uid__') }}".replace('__uid__', data.uid);
                }
            });
        });
    </script>
@endpush

