@extends('layouts.backend.app')
@section('title', 'Edit Staff')
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('label.edit_staff') }}</h6>
            </div>
            <div class="card-body">
                @include('pages.backend.user.staff._form', [
                    'action' => route('backend.user.update', $user->uid),
                    'method' => 'PUT',
                    'user' => $user,
                    'roles' => $roles,
                    'userRoles' => $user->roles->pluck('id')->toArray(),
                    'userTopics' => $user->topics->pluck('id')->toArray()
                ])
            </div>
        </div>
    </div>
@endsection
@include('pages.backend.user.staff.form_scripts')
