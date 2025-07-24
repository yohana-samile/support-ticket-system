@extends('layouts.backend.app')
@section('title', __('label.saa_app_details'))

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="h6 mb-0 font-weight-bold text-gray-800">
                        <i class="fas fa-bookmark mr-2 text-primary"></i>
                        {{__('label.saa_app_details')}}
                    </h5>
                </div>
                <a href="{{ route('backend.saas_app.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> {{__('label.back_to_list')}}
                </a>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-lg-8">
                        <div class="detail-card mb-4">
                            <div class="detail-header">
                                <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i> {{__('label.basic_information')}}</h6>
                            </div>
                            <div class="detail-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">{{__('label.name')}}</label>
                                            <p class="detail-value">{{ $saasApp->name }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">{{__('label.abbreviation')}}</label>
                                            <p class="detail-value">{{ $saasApp->abbreviation }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meta Information Sidebar -->
                    <div class="col-lg-4">
                        <div class="detail-card">
                            <div class="detail-header">
                                <h6 class="mb-0"><i class="fas fa-history mr-2"></i> {{__('label.system_information')}}</h6>
                            </div>
                            <div class="detail-body">
                                <div class="detail-item">
                                    <label class="detail-label">{{__('label.created_at')}}</label>
                                    <p class="detail-value">
                                        {{ $saasApp->created_at->format('M d, Y') }}
                                        <small class="text-muted d-block">{{ $saasApp->created_at->format('h:i A') }}</small>
                                    </p>
                                </div>
                                <div class="detail-item">
                                    <label class="detail-label">{{__('label.updated_at')}}</label>
                                    <p class="detail-value">
                                        {{ $saasApp->updated_at->format('M d, Y') }}
                                        <small class="text-muted d-block">{{ $saasApp->updated_at->format('h:i A') }}</small>
                                    </p>
                                </div>
                                <div class="detail-item">
                                    <label class="detail-label">{{__('label.last_activity')}}</label>
                                    <p class="detail-value">
                                        {{ $saasApp->updated_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end border-top pt-3">
                    <a href="{{ route('backend.saas_app.edit', $saasApp->uid) }}" class="btn btn-primary mr-2">
                        <i class="fas fa-edit mr-1"></i> {{__('label.edit')}}
                    </a>
                    <a href="{{ route('backend.saas_app.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list mr-1"></i> {{__('label.view_all_app')}}
                    </a>
                </div>
            </div>
        </div>
    </div>


    <style>
        .detail-card {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            margin-bottom: 1.5rem;
        }
        .detail-header {
            background-color: #f8f9fc;
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #e3e6f0;
        }
        .detail-body {
            padding: 1.25rem;
        }
        .detail-item {
            margin-bottom: 1rem;
        }
        .detail-item:last-child {
            margin-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }
        .detail-value {
            font-size: 1rem;
            color: #343a40;
            margin-bottom: 0;
        }
        .description-content {
            min-height: 100px;
        }
    </style>
@endsection

@push('style')

@endpush
