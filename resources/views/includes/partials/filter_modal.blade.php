<div class="modal fade" id="reportFilterModal" tabindex="-1" role="dialog" aria-labelledby="reportFilterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportFilterModalLabel">{{ __('Filter Report') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="reportFilterForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('Cut-off Period') }}</label>
                        <select class="form-control" name="cutoff_period">
                            <option value="">{{ __('Select Period') }}</option>
                            <option value="3">{{ __('Last 3 Months') }}</option>
                            <option value="6">{{ __('Last 6 Months') }}</option>
                            <option value="12">{{ __('Last 12 Months') }}</option>
                            <option value="custom">{{ __('Custom Range') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>{{ __('Date Range') }}</label>
                        <div class="input-daterange input-group">
                            <input type="text" class="form-control" name="start_date" id="startDate" placeholder="{{ __('Start Date') }}">
                            <div class="input-group-append">
                                <span class="input-group-text">{{ __('to') }}</span>
                            </div>
                            <input type="text" class="form-control" name="end_date" id="endDate" placeholder="{{ __('End Date') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">{{ __('label.cancel') }}</button>
                    <button type="button" class="btn btn-sm btn-warning" id="clearFiltersModal">
                        <i class="fas fa-times"></i> {{ __('label.clear') }}
                    </button>
                    <button type="button" class="btn btn-sm btn-primary" id="applyFilters">
                        <i class="fas fa-check"></i> {{ __('label.apply') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('after-style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <script>
        let table;

        $(document).ready(function() {
            //initialize datepicker
            $('.input-daterange').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            // Handle cutoff period selection
            $('select[name="cutoff_period"]').change(function() {
                if ($(this).val() && $(this).val() !== 'custom') {
                    const months = parseInt($(this).val());
                    const endDate = new Date();
                    const startDate = new Date();
                    startDate.setMonth(endDate.getMonth() - months);

                    $('#startDate').datepicker('update', startDate);
                    $('#endDate').datepicker('update', endDate);
                }
            });

            // Apply filters
            $('#applyFilters').click(function() {
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();
                const cutoffPeriod = $('select[name="cutoff_period"]').val();

                if(cutoffPeriod === 'custom' && (!startDate || !endDate)) {
                    toastr.error('Please select both start and end dates for custom range', '', {
                        timeout: 3000,
                        positionClass: 'toast-bottom-right'
                    });
                    return;
                }

                if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                    toastr.info('Start date cannot be after end date', '', {
                        timeOut: 3000,
                        positionClass: 'toast-bottom-right'
                    });
                    return;
                }

                table.ajax.reload();
                $('#reportFilterModal').modal('hide');
                toastr.success('Filters applied successfully');
            });

            $('#clearFiltersModal').click(function() {
                resetFilters();
                table.ajax.reload();
                toastr.success('Filters cleared');
            });

            function resetFilters() {
                $('#startDate').val('');
                $('#endDate').val('');
                $('select[name="cutoff_period"]').val('').trigger('change');

                $('#startDate').datepicker('clearDates');
                $('#endDate').datepicker('clearDates');

                table.search('').draw();
            }


            /**
             * Export data
             */

            // Export functionality
            $('#exportData').click(function() {
                const titleMap = {
                    'Saas Applications Summary': 'saas_app',
                    'Topic Summary Count': 'topic',
                    'MNOs Summary Count': 'mno',
                    'Payment Channels Summary Count': 'payment_channel',
                    'All tickets reports': 'all_report',
                    'List Tickets By Mnos': 'ticket_list_by_mno'
                };
                const currentTitle = $('.card-header-title').text().trim();
                const exportType = titleMap[currentTitle];
                const exportUrl = "{{ route('backend.report.export_summary') }}?type=" + exportType;

                const filters = {
                    start_date: $('#startDate').val(),
                    end_date: $('#endDate').val(),
                    // Add other filters as needed
                };

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = exportUrl;
                form.target = '_blank';
                form.style.display = 'none';

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                for (const key in filters) {
                    if (filters[key]) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = filters[key];
                        form.appendChild(input);
                    }
                }

                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            });
        });
    </script>
@endpush
