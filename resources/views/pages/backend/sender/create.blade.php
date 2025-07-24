@extends('layouts.backend.app')
@section('title', 'saas_app_create')
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{__('label.add_sender')}}</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('backend.sender_id.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="sender_id">{{__('label.sender_id')}} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('sender_id') is-invalid @enderror" id="sender_id" name="sender_id" value="{{ old('sender_id') }}" required>
                        @error('sender_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                {{__('active')}}
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{__('label.submit')}}
                    </button>
                    <a href="{{ route('backend.sender_id.index') }}" class="btn btn-secondary">
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
