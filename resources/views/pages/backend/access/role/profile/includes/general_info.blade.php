<div class="row">
    <div class="col-md-9">
        <legend class="legend-sm" style="background-color: lightgray; color: grey;">
            {{ __('label.general_info') }}
        </legend>

        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-striped" id="general_info">
                    <tbody>
                    <tr>
                        <td width="160px">@lang('label.roles'):</td>
                        <td>{{ $role->name }}</td>
                    </tr>
                    <tr>
                        <td width="160px">@lang('label.descriptions'):</td>
                        <td>{{ $role->description }}</td>
                    </tr>
                    <tr>
                        <td width="160px">@lang('label.status'):</td>
                        <td>
                            @if($role->isactive === 1)
                                <span class="badge badge-success">
                                        {{ config('constants.options.yes') }}
                                    </span>
                            @else
                                <span class="badge badge-warning">
                                        {{ config('constants.options.no') }}
                                    </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="160px">@lang('label.is_admin'):</td>
                        <td>
                            @if($role->isadmin === 1)
                                <span class="badge badge-success">
                                    {{ config('constants.options.yes') }}
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    {{ config('constants.options.no') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>

                {{ Html::labels('permission_id', __('label.permission'), ['class' => 'col-form-label']) }}

                <table class="table permissionTable border rounded bg-white overflow-hidden shadow my-4 p-4" style="width: 100%;">
                    <thead>
                    <tr>
                        <th class="p-2">{{ __('label.group') }}</th>
                        <th class="p-2">{{ __('Available permissions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($permissions as $groupId => $groupPermissions)
                        <tr>
                            <td class="p-2">
                                {{ $groupPermissions->first()->permissionGroup->name ?? __('No Group Assigned') }}
                            </td>
                            <td class="p-2">
                                @if($groupPermissions->isNotEmpty())
                                    <p class="mt-2">{{ implode(', ', $groupPermissions->pluck('display_name')->toArray()) }}</p>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <legend class="legend-sm" style="background-color: lightgray; color: grey;">
            {{ __('label.sidebar_summary') }}
        </legend>
        <table class="table table-striped table-bordered" id="sidebar_summary">
            <tbody>
            <tr>
                <td width="130px">{{ '' }}</td>
                <td>{{ '' }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
