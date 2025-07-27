@extends('layouts.backend.app')
@section('title', 'saas_app_create')
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{__('label.create')}}</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('backend.saas_app.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="name">{{__('label.name')}} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="abbreviation">{{__('label.abbreviation')}} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('abbreviation') is-invalid @enderror" id="abbreviation" name="abbreviation" value="{{ old('abbreviation') }}" required>
                        @error('abbreviation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{__('label.submit')}}
                    </button>
                    <a href="{{ route('backend.saas_app.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> {{__('label.cancel')}}
                    </a>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
@endpush

@push('scripts')
@endpush
