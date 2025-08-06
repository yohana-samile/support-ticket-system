@extends('layouts.backend.app')
@section('title', 'sender_information')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{__('label.sender_information')}}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('backend.sender_id.update', $sender->uid) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="sender_id" class="form-label">{{__('label.name')}} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('sender_id') is-invalid @enderror" id="sender_id" name="sender_id" value="{{ old('sender_id', $sender->sender_id) }}" required>
                                @error('sender_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="">{{__('label.status')}}</div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ $sender->is_active ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">
                                        {{ $sender->is_active ? 'Active' : 'Inactive' }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{__('label.update')}}
                                </button>
                                <a href="{{ route('backend.sender_id.index') }}" class="btn btn-secondary">
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
                        <h6 class="m-0 font-weight-bold text-primary">{{__('label.sender_details')}}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>{{__('label.sender')}}:</strong>
                            <span class="text-muted">{{ $sender->sender_id }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>{{__('label.created_at')}}:</strong>
                            <span class="text-muted">{{ $sender->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>{{__('label.updated_at')}}:</strong>
                            <span class="text-muted">{{ $sender->updated_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
