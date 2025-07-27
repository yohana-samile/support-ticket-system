@extends('layouts.backend.app')
@section('title', __('label.topic_details'))

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="h6 mb-0 font-weight-bold text-gray-800">
                        <i class="fas fa-bookmark mr-2 text-primary"></i>
                        {{__('label.topic_details')}}
                    </h5>
                </div>
                <a href="{{ route('backend.topic.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> {{__('label.back_to_list')}}
                </a>
            </div>

            <div class="card-body">
                <!-- Main Details Section -->
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
                                            <p class="detail-value">{{ $topic->name }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">{{__('label.status')}}</label>
                                            <p class="detail-value">
                                            <span class="badge badge-pill {{ $topic->is_active ? 'badge-success' : 'badge-danger' }}">
                                                {{ $topic->is_active ? __('label.active') : __('label.inactive') }}
                                            </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description Section -->
                        <div class="detail-card">
                            <div class="detail-header">
                                <h6 class="mb-0"><i class="fas fa-align-left mr-2"></i> Description</h6>
                            </div>
                            <div class="detail-body">
                                <div class="detail-item">
                                    <div class="description-content bg-light p-3 rounded">
                                        {!! $topic->description ? nl2br(e($topic->description)) : '<span class="text-muted">'.__('label.no_description_provided').'</span>' !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meta Information Sidebar -->
                    <div class="col-lg-4">
                        <div class="detail-card">
                            <div class="detail-header">
                                <h6 class="mb-0"><i class="fas fa-history mr-2"></i> System Information</h6>
                            </div>
                            <div class="detail-body">
                                <div class="detail-item">
                                    <label class="detail-label">{{__('label.created_at')}}</label>
                                    <p class="detail-value">
                                        {{ $topic->created_at->format('M d, Y') }}
                                        <small class="text-muted d-block">{{ $topic->created_at->format('h:i A') }}</small>
                                    </p>
                                </div>
                                <div class="detail-item">
                                    <label class="detail-label">{{__('label.updated_at')}}</label>
                                    <p class="detail-value">
                                        {{ $topic->updated_at->format('M d, Y') }}
                                        <small class="text-muted d-block">{{ $topic->updated_at->format('h:i A') }}</small>
                                    </p>
                                </div>
                                <div class="detail-item">
                                    <label class="detail-label">Last Activity</label>
                                    <p class="detail-value">
                                        {{ $topic->updated_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end border-top pt-3">
                    <a href="{{ route('backend.topic.edit', $topic->uid) }}" class="btn btn-primary mr-2">
                        <i class="fas fa-edit mr-1"></i> {{__('label.edit')}}
                    </a>
                    <a href="{{ route('backend.topic.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list mr-1"></i> {{__('label.view_all')}}
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
