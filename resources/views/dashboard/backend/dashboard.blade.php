@extends('layouts.backend.app')
@section('title', 'Ticketing Dashboard')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-end mb-4">
            <a href="#" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
            </a>
        </div>

        <!-- Key Metrics Cards -->
        <div class="row">
            <!-- Total Tickets -->
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

            <!-- Open Tickets -->
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

            <!-- Resolved Tickets -->
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

            <!-- Reopened Tickets -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Reopened Tickets</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $reopenPercentage }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-redo fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row">
            <!-- Ticket Status Overview -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Ticket Status Overview</h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                 aria-labelledby="dropdownMenuLink">
                                <div class="dropdown-header">View Options:</div>
                                <a class="dropdown-item" href="#">This Week</a>
                                <a class="dropdown-item" href="#">This Month</a>
                                <a class="dropdown-item" href="#">This Year</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="ticketStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ticket Categories -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Ticket Categories</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="ticketCategoryChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            @foreach($categories as $category)
                                <span class="mr-2">
                                    <i class="fas fa-circle" style="color: {{ $category['color'] }}"></i> {{ $category['name'] }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reopen Analysis Section -->
        <div class="row">
            <!-- Reopen Statistics -->
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Reopen Analysis</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="chart-pie pt-4 pb-2">
                                    <canvas id="reopenDistributionChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Reopen Frequency</h6>
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Never reopened
                                        <span class="badge badge-primary">{{ $reopenStats['never_reopened'] }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Reopened once
                                        <span class="badge badge-warning">{{ $reopenStats['reopened_once'] }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Reopened twice
                                        <span class="badge badge-warning">{{ $reopenStats['reopened_twice'] }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Frequently reopened (3+)
                                        <span class="badge badge-danger">{{ $reopenStats['frequent_reopens'] }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Problem Tickets Section -->
        @if($reopenStats['frequent_reopens'] > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-danger">Frequently Reopened Tickets (3+ times)</h6>
                            <span class="badge badge-danger">{{ $reopenStats['frequent_reopens'] }} Tickets</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th>Ticket ID</th>
                                        <th>Subject</th>
                                        <th>Category</th>
                                        <th>Reopen Count</th>
                                        <th>Last Reopened</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($frequentlyReopenedTickets as $ticket)
                                        <tr>
                                            <td>#{{ $ticket->ticket_number }}</td>
                                            <td>{{ $ticket->title }}</td>
                                            <td>{{ $ticket->category->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-danger">{{ $ticket->reopen_history_count }}</span>
                                            </td>
                                            <td>{{ $ticket->updated_at->diffForHumans() }}</td>
                                            <td>
                                                <span class="badge badge-{{ $ticket->status_badge }}">{{ $ticket->status }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('backend.ticket.show', $ticket->uid) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Recent Tickets -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Tickets</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Subject</th>
                                    <th>Requester</th>
                                    <th>Status</th>
                                    <th>Reopens</th>
                                    <th>Priority</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($recentTickets as $ticket)
                                    <tr>
                                        <td>#{{ $ticket->ticket_number }}</td>
                                        <td>{{ $ticket->title }}</td>
                                        <td>{{ $ticket->reported_customer }}</td>
                                        <td>
                                            <span class="badge badge-{{ $ticket->status_badge }}">{{ $ticket->status }}</span>
                                        </td>
                                        <td>
                                            @if($ticket->reopen_history_count > 0)
                                                <span class="badge badge-warning">{{ $ticket->reopen_history_count }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $ticket->priority_badge }}">{{ $ticket->priority }}</span>
                                        </td>
                                        <td>{{ $ticket->created_at->diffForHumans() }}</td>
                                        <td>
                                            <a href="{{ route('backend.ticket.show', $ticket->uid) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Ticket Status Chart
            var ctx = document.getElementById('ticketStatusChart').getContext('2d');
            var ticketStatusChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Open', 'In Progress', 'Resolved', 'Closed', 'Reopened'],
                    datasets: [{
                        label: 'Tickets',
                        data: [
                            {{ $statusCounts['open'] }},
                            {{ $statusCounts['in_progress'] }},
                            {{ $statusCounts['resolved'] }},
                            {{ $statusCounts['closed'] }},
                            {{ $statusCounts['reopened'] }}
                        ],
                        backgroundColor: [
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)',
                            'rgba(255, 99, 132, 0.7)'
                        ],
                        borderColor: [
                            'rgba(255, 159, 64, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Ticket Category Chart
            var ctx2 = document.getElementById('ticketCategoryChart').getContext('2d');
            var ticketCategoryChart = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(collect($categories)->pluck('name')) !!},
                    datasets: [{
                        data: {!! json_encode(collect($categories)->pluck('count')) !!},
                        backgroundColor: {!! json_encode(collect($categories)->pluck('color')) !!},
                        hoverBackgroundColor: {!! json_encode(collect($categories)->pluck('hover_color')) !!},
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    cutout: '70%',
                },
            });

            // Reopen Distribution Chart
            var ctx3 = document.getElementById('reopenDistributionChart').getContext('2d');
            var reopenDistributionChart = new Chart(ctx3, {
                type: 'pie',
                data: {
                    labels: ['Never Reopened', 'Reopened Once', 'Reopened Twice', 'Frequently Reopened'],
                    datasets: [{
                        data: [
                            {{ $reopenStats['never_reopened'] }},
                            {{ $reopenStats['reopened_once'] }},
                            {{ $reopenStats['reopened_twice'] }},
                            {{ $reopenStats['frequent_reopens'] }}
                        ],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(255, 99, 132, 0.7)'
                        ],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    },
                    cutout: '50%',
                },
            });
        </script>
    @endpush
@endsection
