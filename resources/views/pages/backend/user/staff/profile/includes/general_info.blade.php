<div class="row">
    <!-- General Info -->
    <div class="col-md-7">
        <div class="card mb-3">
            <div class="card-header bg-light text-muted">
                <strong>{{ __('label.general_info') }}</strong>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <th>@lang('label.name')</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('label.email')</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>@lang('label.phone')</th>
                            <td>{{ $user->phone }}</td>
                        </tr>
                        <tr>
                            <th>@lang('label.is_manager')</th>
                            <td>
                                @if($user->is_super_admin === config('constants.options.yes'))
                                    <span class="badge bg-primary text-white">{{ config('constants.options.yes') }}</span>
                                @else
                                    <span class="badge bg-danger text-white">{{ config('constants.options.no') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>@lang('label.department')</th>
                            <td>{{ $user->department }}</td>
                        </tr>
                        <tr>
                            <th>@lang('label.favorite_count')</th>
                            <td>{{ $user->favorite_count ?? 0 }}</td>
                        </tr>
                        <tr>
                            <th>@lang('label.topic_of_specialization')</th>
                            <td>
                                @if($user->topics->count())
                                    {{ $user->topics->pluck('name')->implode(', ') }}
                                    <a href="#" class="ms-2 text-primary" data-toggle="modal" data-target="#editTopicsModal-{{ $user->uid }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @else
                                    -
                                    <a href="#" class="ms-2 text-primary" data-toggle="modal" data-target="#editTopicsModal-{{ $user->uid }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>@lang('label.two_factor_secret')</th>
                            <td>{{ $user->two_factor_secret ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('label.two_factor_recovery_codes')</th>
                            <td>{{ $user->two_factor_recovery_codes ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('label.two_factor_confirmed_at')</th>
                            <td>{{ $user->two_factor_confirmed_at ?? '' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar Summary -->
    <div class="col-md-5">
        <div class="card mb-3">
            <div class="card-header bg-light text-muted">
                <strong>{{ __('label.sidebar_summary') }}</strong>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <tbody>
                    <tr>
                        <th>@lang('label.created_at')</th>
                        <td>{{ $user->created_at }}</td>
                    </tr>
                    <tr>
                        <th>@lang('label.updated_at')</th>
                        <td>{{ $user->updated_at }}</td>
                    </tr>
                    <tr>
                        <th>@lang('label.is_password_updated')</th>
                        <td>
                            @if($user->is_password_updated === true)
                                <span class="badge badge-danger">{{ config('constants.options.yes') }}</span>
                            @else
                                <span class="badge badge-primary">{{ config('constants.options.no') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('label.email_verified_at')</th>
                        <td>
                            @if($user->email_verified_at === null)
                                <span class="text-danger">{{ __('label.email_not_verified') }}</span>
                            @else
                                {{ $user->email_verified_at }}
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Roles -->
        <div class="card">
            <div class="card-header bg-light text-muted">
                <strong>@lang('label.roles')</strong>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <td>
                                @if($user->roles->isNotEmpty())
                                    @foreach($user->roles as $role)
                                        {{ $role->name }}@if (!$loop->last), @endif
                                    @endforeach
                                @else
                                    {{ __('No roles assigned') }}
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div class="custom-control custom-switch">
                                    <input
                                        type="checkbox"
                                        class="custom-control-input user-status-toggle"
                                        id="userStatusSwitch"
                                        data-id="{{ $user->id }}"
                                        {{ $user->is_active == 1 ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="userStatusSwitch">
                                        {{ $user->is_active ? 'Enabled' : 'Disabled' }}
                                    </label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for editing topics -->
    <div class="modal fade" id="editTopicsModal-{{ $user->uid }}" tabindex="-1" aria-labelledby="editTopicsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTopicsModalLabel">Edit Topics for {{ $user->name }}</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('backend.user.update_topics', $user->uid) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="list-group">
                            @foreach($topics as $topic)
                                <label class="list-group-item">
                                    <input class="form-check-input me-2"
                                           type="checkbox"
                                           name="topics[]"
                                           value="{{ $topic->id }}"
                                        {{ $user->topics->contains($topic->id) ? 'checked' : '' }}>
                                    {{ $topic->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('label.cancel')}}</button>
                        <button type="submit" class="btn btn-primary">{{__('label.save_changes')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
