<div class="card shadow mb-4">
    @include('pages.backend.report.partials.top_header_button')
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="paymentChannelTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                <tr>
                    <th width="20%">{{ __('label.payment_channel') }}</th>
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


<script>
    $(document).ready(function() {
        table = $('#paymentChannelTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('backend.report.payment_channel_data') }}",
                data: function(d) {
                    d.start_date = $('#startDate').val();
                    d.end_date = $('#endDate').val();
                }
            },
            columns: [
                {
                    data: 'name',
                    name: 'name', // This is the only searchable column
                    orderable: true,
                    render: function(data, type, row) {
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
                    className: function(row, type, val, meta) {
                        return val > 0 ? 'text-{{ $status->color_class ?? "primary" }}' : '';
                    }
                },
                @endforeach
            ],
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var total = api
                    .column(1, { page: 'current' })
                    .data()
                    .reduce(function(a, b) {
                        return parseInt(a) + parseInt(b);
                    }, 0);
                $('#totalTickets').text(total);
            }
        });
    });
</script>

