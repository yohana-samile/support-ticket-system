<div class="card-header py-3 d-flex justify-content-between align-items-center bg-light">
    <h6 class="m-0 font-weight-bold card-header-title">{{ $title }}</h6>
    <div>
        <span class="badge badge-light mr-2">
            <i class="fas fa-ticket-alt"></i> Total: <span id="totalTickets">0</span>
        </span>
        <button class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#reportFilterModal">
            <i class="fas fa-filter"></i> {{ __('label.filter') }}
        </button>
{{--        <button class="btn btn-sm btn-outline-warning" id="clearFilters">--}}
{{--            <i class="fas fa-times"></i> {{ __('label.clear_filters') }}--}}
{{--        </button>--}}
        <button class="btn btn-sm btn-outline-success" id="exportData">
            <i class="fas fa-file-export"></i> {{ __('label.export') }}
        </button>
        <button class="btn btn-sm btn-outline-primary" onclick="showSummaryCards()">
            <i class="fas fa-arrow-left"></i> {{ __('label.back_to_summary') }}
        </button>
    </div>
</div>

<script>
    let table;

    // Export functionality
    $('#exportData').click(function() {
        const titleMap = {
            'Saas Applications Summary': 'saas_app',
            'Topic Summary': 'topic',
            'MNOs Summary': 'mno',
            'Payment Channels Summary': 'payment_channel',
            'All tickets reports': 'all_report'
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
</script>
