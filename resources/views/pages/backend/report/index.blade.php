@extends('layouts.backend.app')
@section('title', __('label.report_summary'))
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Ticket Summary Dashboard') }}</h6>
            </div>
            <div class="card-body">
                <!-- Summary Cards Section -->
                <div id="summaryCards">
                    <div class="row mb-4">
                        <!-- Saas Apps Summary Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2 pointer" onclick="loadSummary('saas_app')">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                {{ __('Saas Applications') }}</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summaryCounts['saas_apps'] ?? 0 }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-cloud fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Topics Summary Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2 pointer" onclick="loadSummary('topic')">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                {{ __('Topics') }}</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summaryCounts['topics'] ?? 0 }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Operators Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2 pointer" onclick="loadSummary('mno')">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                {{ __('Mobile Operators') }}</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summaryCounts['mnos'] ?? 0 }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-mobile-alt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Channels Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2 pointer" onclick="loadSummary('payment_channel')">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                {{ __('Payment Channels') }}</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summaryCounts['payment_channels'] ?? 0 }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2 pointer" onclick="loadHistory()">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                {{ __('label.report_history') }}</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">  {{ $summaryCounts['total_tickets'] ?? 0 }} </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-database fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Details Section -->
                <div id="summaryDetails" class="mt-4" style="display: none;">
                    <!-- Content will be loaded dynamically via AJAX -->
                </div>

                <!-- Initial placeholder (shown only when page loads) -->
                <div id="initialPlaceholder" class="text-center text-muted py-5">
                    <i class="fas fa-chart-pie fa-3x mb-3"></i>
                    <p>{{ __('Click on any summary card above to view detailed reports') }}</p>
                </div>
            </div>
        </div>
    </div>
    @include('includes.partials.filter_modal')

@endsection

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">

    <style>
        .pointer { cursor: pointer; }
        .summary-table th { white-space: nowrap; }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.35em 0.65em;
        }
        .clickable-row { cursor: pointer; }
        .clickable-row:hover { background-color: #f8f9fa; }
        .fade-in { animation: fadeIn 0.3s; }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <script>
        // Hide initial placeholder when any content is loaded
        function hideInitialPlaceholder() {
            $('#initialPlaceholder').hide();
            $('#summaryDetails').show().addClass('fade-in');
        }

        function showSummaryCards() {
            $('#summaryCards').show();
            $('#summaryDetails').hide();
            $('#initialPlaceholder').show();
        }

        function hideSummaryCards() {
            $('#summaryCards').hide();
            $('#initialPlaceholder').hide();
        }

        function loadHistory() {
            hideSummaryCards();
            $('#summaryDetails').html(`
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading history...</p>
                </div>
            `);

            $.get("{{ route('backend.report.history') }}", function(data) {
                $('#summaryDetails').html(data);
                hideInitialPlaceholder();
            }).fail(function() {
                $('#summaryDetails').html(`
                    <div class="alert alert-danger">
                        Failed to load history. Please try again.
                    </div>
                `);
                hideInitialPlaceholder();
            });
        }

        function loadSummary(type, id = null) {
            hideSummaryCards();
            const url = id
                ? `{{ route('backend.report.history') }}?type=${type}&id=${id}`
                : `{{ route('backend.report.summary') }}?type=${type}`;

            $('#summaryDetails').html(`
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading ${type} summary...</p>
                </div>
            `);

            $.get(url, function(data) {
                $('#summaryDetails').html(data);
                hideInitialPlaceholder();

                // Add back button functionality if this is a detail view
                if (id) {
                    $('.back-to-summary').on('click', function() {
                        showSummaryCards();
                    });
                }
            }).fail(function() {
                $('#summaryDetails').html(`
                    <div class="alert alert-danger">
                        Failed to load ${type} summary. Please try again.
                    </div>
                `);
                hideInitialPlaceholder();
            });
        }

        // Handle click on summary table rows
        $(document).on('click', '.clickable-row', function() {
            const type = $(this).data('type');
            const id = $(this).data('id');
            loadSummary(type, id);
        });

        // Initialize with summary cards shown and details hidden
        $(document).ready(function() {
            $('#summaryDetails').hide();
        });
    </script>
@endpush
