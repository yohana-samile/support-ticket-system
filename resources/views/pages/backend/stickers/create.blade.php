@extends('layouts.backend.app')
@section('title', 'sticker_create')
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{__('label.add_sticker')}}</h6>
            </div>
            <div class="card-body">
                @include('layouts.partials._sticker_form')
            </div>
        </div>
    </div>
@endsection

@push('styles')
@endpush

@push('scripts')
@endpush
