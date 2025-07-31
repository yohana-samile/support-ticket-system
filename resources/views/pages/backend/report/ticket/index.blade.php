@extends('layouts.backend.app')
@section('title', __('label.report_summary'))
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('label.tickets_report') }}</h6>
            </div>
            <div class="card-body">
                <div id="summaryCards">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>{{__('label.title')}}</th>
                                <th>{{__('label.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="pointer">
                                <td>
                                    <a href="{{ route('backend.report.all_reports') }}" class="text-decoration-none text-danger font-weight-bold d-block">
                                        {{ __('label.all_tickets_reports') }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('backend.report.all_reports') }}" class="text-decoration-none text-muted">
                                        {{__('label.view')}}
                                    </a>
                                </td>
                            </tr>

                            <tr class="pointer">
                                <td>
                                    <a href="{{ route('backend.report.by_saas_app') }}" class="text-decoration-none text-primary font-weight-bold d-block">
                                        {{ __('Report by Saas Applications summary count') }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('backend.report.all_reports') }}" class="text-decoration-none text-muted">
                                        {{__('label.view')}}
                                    </a>
                                </td>
                            </tr>

                            <tr class="pointer">
                                <td>
                                    <a href="{{ route('backend.report.by_topic') }}" class="text-decoration-none text-success font-weight-bold d-block">
                                        {{ __('Report by Topics summary count') }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('backend.report.all_reports') }}" class="text-decoration-none text-muted">
                                        {{__('label.view')}}
                                    </a>
                                </td>
                            </tr>

                            <tr class="pointer">
                                <td>
                                    <a href="{{ route('backend.report.by_mno') }}" class="text-decoration-none text-warning font-weight-bold d-block">
                                        {{ __('Report by Mobile Operators & summary count') }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('backend.report.all_reports') }}" class="text-decoration-none text-muted">
                                        {{__('label.view')}}
                                    </a>
                                </td>
                            </tr>

                            <tr class="pointer">
                                <td>
                                    <a href="{{ route('backend.report.by_payment_channel') }}" class="text-decoration-none text-primary font-weight-bold d-block">
                                        {{ __('Report by Payment Channels summary count') }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('backend.report.all_reports') }}" class="text-decoration-none text-muted">
                                        {{__('label.view')}}
                                    </a>
                                </td>
                            </tr>
                            <tr class="pointer">
                                <td>
                                    <a href="{{ route('backend.report.by_filter') }}" class="text-decoration-none text-info font-weight-bold d-block">
                                        {{ __('Report Filter') }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('backend.report.all_reports') }}" class="text-decoration-none text-muted">
                                        {{__('label.view')}}
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

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
    <script>
        $(document).ready(function () {
            $('table.table').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search reports..."
                }
            });
        });
    </script>
@endpush
