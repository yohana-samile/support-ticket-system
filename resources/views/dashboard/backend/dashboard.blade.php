@extends('layouts.backend.app')
@section('title', 'Ticketing Dashboard')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('backend.ticket.create') }}" class="btn btn-sm btn-primary shadow-sm me-2">
                <i class="fas fa-plus-circle fa-sm text-white-50"></i> Create New Ticket
            </a>

            <a href="#" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
            </a>
        </div>
    </div>
@endsection
