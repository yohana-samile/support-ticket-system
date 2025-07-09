@extends('layouts.backend.app')
@section('title', 'Create New Ticket')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Create New Ticket</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('backend.ticket.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            @include('pages.backend.ticket._form', [
                               'categories' => $categories,
                               'users' => $users,
                               'customers' => $customers,
                               'buttonText' => 'Create Ticket'
                           ])

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

