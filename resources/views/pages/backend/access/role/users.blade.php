@extends('layouts.backend.app')
@section('title', __('label.role_users'))
@section('content')
    <div class="container-fluid">
        <div id="content">
            <h1 class="h4 font-weight-bold mb-4">Users with Role: {{ $role->display_name }}</h1>
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{ __('label.name') }}</th>
                                    <th>{{ __('label.email') }}</th>
                                    <th>{{ __('label.roles') }}</th>
                                    <th>{{ __('label.permission') }}</th>
                                    <th>{{ __('label.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->roles->pluck('display_name')->join(', ') }}</td>
                                        <td>{{ $user->permissions->pluck('display_name')->join(', ') ?: '-' }}</td>
                                        <td>
                                            <div role="group">
                                                <a href="{{ route('backend.user.show', $user->uid) }}" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-eye"></i> {{ __('label.view') }}
                                                </a>
                                                <a href="{{ route('backend.user.edit', $user->uid) }}" class="btn btn-sm btn-warning">
                                                    <i class="fa fa-edit"></i> {{ __('label.edit') }}
                                                </a>
                                                <a href="{{ route('backend.user.caused_activity', $user->uid) }}" class="btn btn-sm btn-success">
                                                    <i class="fa fa-history"></i> {{ __('label.caused_activity') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No users found for this role.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
    </script>
@endpush
