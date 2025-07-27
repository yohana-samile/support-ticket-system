@extends('layouts.backend.app')
@section('title', 'edit_saas_app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{__('label.saas_information')}}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('backend.saas_app.update', $saasApp->uid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name" class="form-label">{{__('label.topic_name')}} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $saasApp->name) }}" required>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="abbreviation" class="form-label">{{__('label.abbreviation')}} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('abbreviation') is-invalid @enderror" id="abbreviation" name="abbreviation" value="{{ old('abbreviation', $saasApp->abbreviation) }}" required>
                                @error('abbreviation')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{__('label.update')}}
                                </button>
                                <a href="{{ route('backend.saas_app.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> {{__('label.cancel')}}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{__('label.system_information')}}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>{{__('label.name')}}:</strong>
                            <span class="text-muted">{{ $saasApp->name ?? 'Not assigned' }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>{{__('label.created_at')}}:</strong>
                            <span class="text-muted">{{ $saasApp->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>{{__('label.updated_at')}}:</strong>
                            <span class="text-muted">{{ $saasApp->updated_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
