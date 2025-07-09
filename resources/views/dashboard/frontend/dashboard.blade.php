@extends('layouts.frontend.app')
@section('title', 'Dashboard')
@section('content')
    @php
        $dashboardData = Cache::remember('dashboard_metrics', now()->addMinutes(1), function() {
            $reporterId = auth()->id();
            $data = [
                'totalIncidents' => \App\Models\Incident::where('reporter_id', $reporterId)->count(),
                'totalVictims' => \App\Models\Victim::whereHas('incident', function($q) use ($reporterId) {
                    $q->where('reporter_id', $reporterId);
                })->count(),
                'incidentsThisMonth' => \App\Models\Incident::where('reporter_id', $reporterId)
                    ->whereMonth('created_at', now()->month)
                    ->count(),
                'resolvedCases' => \App\Models\Incident::where('reporter_id', $reporterId)
                    ->whereHas('statusModel', function($q) {
                        $q->where('slug', 'resolved');
                    })->count(),
                'incidentsByType' => \App\Models\Incident::selectRaw('type, count(*) as count')
                    ->where('reporter_id', $reporterId)
                    ->groupBy('type')
                    ->orderBy('count', 'desc')
                    ->get()
                    ->map(function($item) {
                        $item->formatted_type = ucwords(str_replace('_', ' ', $item->type));
                        return $item;
                    }),
                'incidentsByStatus' => \App\Models\Incident::with('statusModel')
                    ->selectRaw('status, count(*) as count')
                    ->where('reporter_id', $reporterId)
                    ->groupBy('status')
                    ->get(),
                'monthlyTrend' => \App\Models\Incident::selectRaw('
                        YEAR(occurred_at) as year,
                        MONTH(occurred_at) as month,
                        COUNT(*) as count')
                    ->where('reporter_id', $reporterId)
                    ->where('occurred_at', '>', now()->subYear())
                    ->groupBy('year', 'month')
                    ->orderBy('year')
                    ->orderBy('month')
                    ->get()
            ];

            if (isset($data['monthlyTrend'])) {
                $data['monthlyTrend'] = $data['monthlyTrend']->map(function($item) {
                    $date = DateTime::createFromFormat('!m', $item->month);
                    $item->month_name = $date->format('M');
                    $item->label = $item->month_name . ' ' . $item->year;
                    return $item;
                });
            }

            return $data;
        });

        extract($dashboardData);
    @endphp

    <div class="container mx-auto px-4 py-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                My Reporter Dashboard
            </h2>
            <p class="text-gray-600">
                Summary of incidents you've reported
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @include('dashboard.backend.card', [
                'color' => 'red',
                'icon' => 'exclamation',
                'title' => 'My Reported Incidents',
                'value' => $totalIncidents
            ])

            @include('dashboard.backend.card', [
                'color' => 'blue',
                'icon' => 'users',
                'title' => 'Victims in My Cases',
                'value' => $totalVictims
            ])

            @include('dashboard.backend.card', [
                'color' => 'yellow',
                'icon' => 'calendar',
                'title' => 'This Month',
                'value' => $incidentsThisMonth
            ])

            @include('dashboard.backend.card', [
                'color' => 'green',
                'icon' => 'check-circle',
                'title' => 'Resolved Cases',
                'value' => $resolvedCases
            ])
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        My Incidents by Type
                    </h3>
                    <div class="text-sm text-gray-500">{{ $incidentsByType->sum('count') }} total</div>
                </div>
                <div class="h-64">
                    <canvas id="incidentsByTypeChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        My Incidents by Status
                    </h3>
                    <div class="text-sm text-gray-500">{{ $incidentsByStatus->sum('count') }} total</div>
                </div>
                <div class="h-64">
                    <canvas id="incidentsByStatusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    My Reports - Last 12 Months
                </h3>
                <div class="h-64">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        const chartConfig = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                },
                datalabels: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return `${context.label}: ${context.raw} (${Math.round(context.parsed * 100 / context.dataset.data.reduce((a, b) => a + b, 0))}%)`;
                        }
                    }
                }
            }
        };

        @if($incidentsByType->isNotEmpty())
            new Chart(
                document.getElementById('incidentsByTypeChart'),
                {
                    type: 'doughnut',
                    data: {
                        labels: @json($incidentsByType->pluck('formatted_type')),
                        datasets: [{
                            data: @json($incidentsByType->pluck('count')),
                            backgroundColor: [
                                '#EF4444', '#3B82F6', '#F59E0B', '#10B981',
                                '#8B5CF6', '#EC4899', '#14B8A6', '#F97316'
                            ],
                        }]
                    },
                    options: chartConfig
                }
            );
        @else
        document.getElementById('incidentsByTypeChart').closest('.bg-white').innerHTML = `
                <div class="flex flex-col items-center justify-center h-64 text-gray-500">
                    <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>No data available</p>
                </div>
            `;
        @endif

        @if($incidentsByStatus->isNotEmpty())
            new Chart(
                document.getElementById('incidentsByStatusChart'),
                {
                    type: 'bar',
                    data: {
                        labels: @json($incidentsByStatus->map(fn($item) => $item->statusModel?->name ?? ucfirst(str_replace('_', ' ', $item->status)))),
                        datasets: [{
                            label: 'Incidents',
                            data: @json($incidentsByStatus->pluck('count')),
                            backgroundColor: @json($incidentsByStatus->map(function ($item) {
                                    if ($item->statusModel) {
                                        switch ($item->statusModel->color_class) {
                                            case 'bg-yellow-100': return '#FEF9C3';
                                            case 'bg-blue-100': return '#DBEAFE';
                                            case 'bg-green-100': return '#D1FAE5';
                                            case 'bg-gray-100': return '#F3F4F6';
                                            default: return '#3B82F6'; // default blue
                                        }
                                    }
                                    return '#3B82F6';
                                })),
                            borderColor: @json($incidentsByStatus->map(function ($item) {
                                    if ($item->statusModel) {
                                        switch ($item->statusModel->text_color_class) {
                                            case 'text-yellow-800': return '#92400E';
                                            case 'text-blue-800': return '#1E40AF';
                                            case 'text-green-800': return '#065F46';
                                            case 'text-gray-800': return '#1F2937';
                                            default: return '#1E40AF';
                                        }
                                    }
                                    return '#1E40AF';
                                })),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        ...chartConfig,
                        scales: {
                            y: {beginAtZero: true}
                        }
                    }
                }
            );
        @else
            document.getElementById('incidentsByStatusChart').closest('.bg-white').innerHTML = `
                <div class="flex flex-col items-center justify-center h-64 text-gray-500">
                    <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>No data available</p>
                </div>
            `;
        @endif

        @if($monthlyTrend->isNotEmpty())
            new Chart(
                document.getElementById('monthlyTrendChart'),
                {
                    type: 'line',
                    data: {
                        labels: @json($monthlyTrend->pluck('label')),
                        datasets: [{
                            label: 'Incidents',
                            data: @json($monthlyTrend->pluck('count')),
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.05)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {beginAtZero: true}
                        }
                    }
                }
            );
        @else
            document.getElementById('monthlyTrendChart').closest('.bg-white').innerHTML = `
                <div class="flex flex-col items-center justify-center h-64 text-gray-500">
                    <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>No data available</p>
                </div>
            `;
        @endif
    </script>
@endsection
