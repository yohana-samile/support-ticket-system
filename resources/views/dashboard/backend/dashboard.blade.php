@extends('layouts.backend.app')
@section('title', 'Ticketing Dashboard')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-end gap-2 mb-4">
            <!-- Filter Dropdown -->
            <div class="dropdown">
                <button class="btn btn-sm btn-info shadow-sm dropdown-toggle" type="button" id="filterDropdown"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="margin-right: 10px;">
                    <i class="fas fa-filter fa-sm text-white-50"></i> Filter Tickets
                </button>
                <div class="dropdown-menu dropdown-menu-right p-3" style="width: 300px;" aria-labelledby="filterDropdown">
                    <form id="filterForm" method="GET" action="{{ route('home') }}">
                        <!-- Payment Channel Filter -->
                        <div class="form-group">
                            <label for="payment_channel">Payment Channel</label>
                            <select class="form-control select2" id="payment_channel" name="payment_channel">
                                <option value="">All Payment Channels</option>
                                @foreach($paymentChannels as $channel)
                                    <option value="{{ $channel->id }}"
                                        {{ request('payment_channel') == $channel->id ? 'selected' : '' }}>
                                        {{ $channel->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Mobile Operator Filter -->
                        <div class="form-group">
                            <label for="mobile_operator">Mobile Operator</label>
                            <select class="form-control select2" id="mobile_operator" name="mobile_operator">
                                <option value="">All Operators</option>
                                @foreach($mobileOperators as $operator)
                                    <option value="{{ $operator->id }}"
                                        {{ request('mobile_operator') == $operator->id ? 'selected' : '' }}>
                                        {{ $operator->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- SaaS App Filter -->
                        <div class="form-group">
                            <label for="saas_app">SaaS Application</label>
                            <select class="form-control select2" id="saas_app" name="saas_app">
                                <option value="">All Applications</option>
                                @foreach($saasApps as $app)
                                    <option value="{{ $app->id }}"
                                        {{ request('saas_app') == $app->id ? 'selected' : '' }}>
                                        {{ $app->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="form-group">
                            <label for="status">Ticket Status</label>
                            <select class="form-control select2" id="status" name="status">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $slug => $status)
                                    <option value="{{ $slug }}"
                                        {{ request('status') == $slug ? 'selected' : '' }}>
                                        {{ $status['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-check"></i> Apply Filters
                            </button>
                            @if(request()->hasAny(['payment_channel', 'mobile_operator', 'saas_app', 'status']))
                                <a href="{{ route('home') }}" class="btn btn-danger btn-sm">
                                    <i class="fas fa-times"></i> Clear Filters
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Active Filters Badges -->
            @if(request()->hasAny(['payment_channel', 'mobile_operator', 'saas_app', 'status']))
                <div class="d-flex align-items-center">
                    <span class="mr-2">Filters:</span>
                        @if(request('payment_channel'))
                            @php $channel = $paymentChannels->firstWhere('id', request('payment_channel')); @endphp
                            <span class="badge badge-info mr-2">
                            Payment: {{ $channel->name ?? 'Unknown' }}
                            <a href="{{ remove_filter_url('payment_channel') }}" class="text-white ml-1">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif

                    @if(request('mobile_operator'))
                        @php $operator = $mobileOperators->firstWhere('id', request('mobile_operator')); @endphp
                        <span class="badge badge-info mr-2">
                            Operator: {{ $operator->name ?? 'Unknown' }}
                            <a href="{{ remove_filter_url('mobile_operator') }}" class="text-white ml-1">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif

                    @if(request('saas_app'))
                        @php $app = $saasApps->firstWhere('id', request('saas_app')); @endphp
                        <span class="badge badge-info mr-2">
                            App: {{ $app->name ?? 'Unknown' }}
                            <a href="{{ remove_filter_url('saas_app') }}" class="text-white ml-1">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif

                    @if(request('status'))
                        <span class="badge badge-info mr-2">
                            Status: {{ $statuses[request('status')]['name'] ?? ucfirst(request('status')) }}
                            <a href="{{ remove_filter_url('status') }}" class="text-white ml-1">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                </div>
            @endif

            <!-- Create Ticket Button -->
            <a href="{{ route('backend.ticket.create') }}" class="btn btn-sm btn-primary shadow-sm" style="margin-right: 10px;">
                <i class="fas fa-plus-circle fa-sm text-white-50"></i> Create New Ticket
            </a>

            <!-- Export Button -->
            <a href="#" class="btn btn-sm btn-success shadow-sm" id="exportReportBtn">
                <i class="fas fa-download fa-sm text-white-50"></i> Export Report
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Tickets</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTickets }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Resolved Tickets</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $resolvedTickets }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Open Tickets</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $openTickets }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Reopened (%)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reopenPercentage }}%</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-redo fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Ticket Status Distribution</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="statusPieChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            <div class="mt-4 text-center small">
                                @foreach($statusCounts as $status => $count)
                                    @if($count > 0)
                                        <span class="mr-2">
                                            <i class="fas fa-circle {{ $statusColors[$status]['color_class'] ?? 'bg-secondary' }}"></i>
                                            {{ $statusColors[$status]['name'] ?? ucfirst(str_replace('_', ' ', $status)) }} ({{ $count }})
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Reopen Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-bar">
                            <canvas id="reopenBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Tickets and Problem Tickets -->
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Tickets</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Assigned To</th>
                                    <th>Created</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($recentTickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->ticket_number }}</td>
                                        <td>{{ Str::limit($ticket->title, 30) }}</td>
                                        <td>
                                          <span class="badge {{ $statusColors[$ticket->status]['color_class'] ?? 'bg-secondary' }} {{ $statusColors[$ticket->status]['text_color_class'] ?? 'text-white' }}">
                                                {{ $statusColors[$ticket->status]['name'] ?? ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $ticket->assignedTo->name ?? 'Unassigned' }}</td>
                                        <td>{{ $ticket->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Frequently Reopened Tickets</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach($frequentlyReopenedTickets as $ticket)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $ticket->title }}
                                    <span class="badge badge-danger badge-pill">{{ $ticket->reopen_history_count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Your Ticket Stats</h6>
                    </div>
                    <div class="card-body">
                        <h4 class="small font-weight-bold">Assigned Tickets <span class="float-right">{{ $assignedTickets }}</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min($assignedTickets, 100) }}%" aria-valuenow="{{ $assignedTickets }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <h4 class="small font-weight-bold">Overdue Tickets <span class="float-right">{{ $overdueTickets }}</span></h4>
                        <div class="progress">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ min($overdueTickets, 100) }}%" aria-valuenow="{{ $overdueTickets }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ticket Categorization -->
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Top Topics</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach($topics as $topic)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $topic->topic->name ?? 'Unknown' }}
                                    <span class="badge badge-primary badge-pill">{{ $topic->count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Top Subtopics</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach($subtopics as $subtopic)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ optional($subtopic->subtopic)->name ?? 'Unknown' }}
                                    <span class="badge badge-primary badge-pill">{{ $subtopic->count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Top Tertiary Topics</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach($tertiaryTopics as $tertiaryTopic)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $tertiaryTopic->tertiaryTopic->name ?? 'Unknown' }}
                                    <span class="badge badge-primary badge-pill">{{ $tertiaryTopic->count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusColors = @json($statusColors);

        // Bootstrap color class to hex mapping
        const colorClassToHex = {
            'bg-primary': '#0d6efd',
            'bg-secondary': '#6c757d',
            'bg-success': '#198754',
            'bg-danger': '#dc3545',
            'bg-warning': '#ffc107',
            'bg-info': '#0dcaf0',
            'bg-light': '#f8f9fa',
            'bg-dark': '#212529',
            'bg-indigo': '#6610f2'
        };

        // Status Pie Chart
        const pieCtx = document.getElementById("statusPieChart")?.getContext('2d');
        if (pieCtx) {
            // Prepare data for chart - only include statuses that have tickets
            const chartLabels = [];
            const chartData = [];
            const chartColors = [];

            @foreach($statusCounts as $status => $count)
                @if($count > 0)
                    chartLabels.push("{{ $statusColors[$status]['name'] ?? ucfirst(str_replace('_', ' ', $status)) }}");
                    chartData.push({{ $count }});
                    chartColors.push(colorClassToHex[statusColors['{{ $status }}']?.color_class ?? '#6c757d']);
                @endif
            @endforeach

            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartData,
                        backgroundColor: chartColors,
                        hoverBackgroundColor: chartColors.map(color =>
                            Chart.helpers.color(color).lighten(0.2).rgbString()
                        ),
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyColor: "#858796",
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            padding: 15,
                            displayColors: true,
                            caretPadding: 10,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        },
                        legend: {
                            display: false
                        }
                    },
                    cutout: '80%',
                },
            });
        }

        // Reopen Bar Chart
        const barCtx = document.getElementById("reopenBarChart")?.getContext('2d');
        if (barCtx) {
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: ["Never Reopened", "Reopened Once", "Reopened Twice", "Frequent Reopens"],
                    datasets: [{
                        label: "Tickets",
                        backgroundColor: "#4e73df",
                        hoverBackgroundColor: "#2e59d9",
                        borderColor: "#4e73df",
                        data: [
                            {{ $reopenStats['never_reopened'] }},
                            {{ $reopenStats['reopened_once'] }},
                            {{ $reopenStats['reopened_twice'] }},
                            {{ $reopenStats['frequent_reopens'] }}
                        ],
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 6
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                            },
                            grid: {
                                color: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            titleMarginBottom: 10,
                            titleColor: '#6e707e',
                            titleFont: { size: 14 },
                            backgroundColor: "rgb(255,255,255)",
                            bodyColor: "#858796",
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            padding: 15,
                            displayColors: false,
                            caretPadding: 10,
                        }
                    }
                }
            });
        }

        // Export Report Button
        document.getElementById('exportReportBtn')?.addEventListener('click', function(e) {
            e.preventDefault();

            // Get current filters
            const params = new URLSearchParams(window.location.search);

            // Show loading
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating Report...';
            this.disabled = true;

            // Call export endpoint
            fetch(`/backend/dashboard/export?${params.toString()}`)
                .then(response => {
                    if (!response.ok) throw new Error('Export failed');
                    return response.blob();
                })
                .then(blob => {
                    // Create download link
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `ticket-report-${new Date().toISOString().split('T')[0]}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                })
                .catch(error => {
                    alert('Error generating report: ' + error.message);
                })
                .finally(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                });
        });
    });
</script>
