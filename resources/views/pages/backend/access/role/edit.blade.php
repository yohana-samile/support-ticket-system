@extends('layouts.backend.app')
@section('title', __('label.edit_role'))
@section('content')

    {{ Html::formModel($role,['route' => ['backend.role.update',$role->uid], 'method'=>'PUT','autocomplete' => 'off', 'id' => 'update', 'name' => 'edit', 'class' => 'form-horizontal needs-validation', 'novalidate']) }}

    {{ Html::hidden('resource_id', $role->id, []) }}
    {{ Html::hidden('action_type', 2, []) }}
    {{ Html::hidden('today', getTodayDate(), []) }}

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

                    <div class="form-group row mb-2">
                        <div class="col-lg-3 col-md-6">
                            {{ Html::labels('display_name', __('label.name'), ['class' =>'required_asterik col-form-label']) }}
                            {{ Html::texts('display_name',null,['class'=>'form-control required', 'id' => 'name','placeholder' => '', 'autocomplete' => 'off']) }}
                            {!! $errors->first('display_name', '<span class="badge badge-danger">:message</span>') !!}
                        </div>

                        <div class="col-lg-3 col-md-6">
                            {{ Html::labels('description', __('label.descriptions'), ['class' =>'required_asterik col-form-label']) }}
                            {{ Html::texts('description',null,['class'=>'form-control required', 'id' => 'description','placeholder' => '', 'autocomplete' => 'off']) }}
                            {!! $errors->first('description', '<span class="badge badge-danger">:message</span>') !!}
                        </div>
                    </div>

                    <br>

                    {{ Html::labels('permission_id', __('label.permission'), ['class' => 'col-form-label']) }}
                    <table class="table permissionTable border rounded bg-white overflow-hidden shadow my-4 p-4" style="width: 100%;">
                        <thead>
                        <tr>
                            <th class="p-2">{{ __('label.group') }}</th>
                            <th class="p-2">
                                <label>
                                    <input class="grand_selectall" type="checkbox">
                                    {{ __('Select All') }}
                                </label>
                            </th>
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
                                    <label>
                                        <input class="selectall" type="checkbox">
                                        {{ __('Select All') }}
                                    </label>
                                </td>
                                <td class="p-2">
                                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                        @forelse($groupPermissions as $permission)
                                            <label style="display: flex; align-items: center;">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                       class="permissioncheckbox"
                                                    {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                {{ $permission->display_name }}
                                            </label>
                                        @empty
                                            <p>{{ __('label.no_permission_group') }}</p>
                                        @endforelse
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <br/>

                    <div class="form-group row mb-2">
                        <div class="col-lg-3 col-md-6">
                            <div class="form-check">
                                {{ Html::labels('isactive', __('label.is_active'), ['class' => 'form-check-label']) }}
                                <input type="checkbox" name="isactive" class="form-check-input" id="isactive" value="1" @if($role->isactive) checked @endif>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="form-check">
                                {{ Html::labels('isadmin', __('label.is_admin'), ['class' => 'form-check-label']) }}
                                <input type="checkbox" name="isadmin" class="form-check-input" id="isadmin" value="1" @if($role->isadmin) checked @endif>
                            </div>
                        </div>
                    </div>

                    <br/>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="element-form">
                                <div class="form-group pull-right">
                                    {{ link_to_route('backend.role.profile',trans('label.cancel'),[$role->uid],['id'=> 'cancel', 'class' => 'btn btn-dark cancel_button']) }}
                                    {{ Html::submits(trans('label.submit'), ['class' => 'btn btn-primary', 'type'=>'submit', 'style' => 'border-radius: 5px;', 'id' => 'submit_btn']) }}
                                    <label id="submit_label"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {!!  Html::formClose()  !!}
@endsection

@push('scripts')
    <script>
        $(function() {
            pleaseWaitSubmitButton("submit_btn","submit_label","{{ trans('label.please_wait') }}",2);

            $('body').on('submit', 'form[name=create]', function(e) {
                e.preventDefault();
                /*Codes Here*/
                pleaseWaitSubmitButton("submit_btn","submit_label","{{ trans('label.please_wait') }}",1);

                this.submit();

            });
            $(document).ready(function () {

                // Handle individual row select all
                $(".permissionTable").on('click', '.selectall', function () {
                    let isChecked = $(this).is(':checked');
                    $(this).closest('tr').find('.permissioncheckbox').prop('checked', isChecked);
                    updateGrandSelectAll();
                });

                // Handle grand select all (all rows)
                $(".permissionTable").on('click', '.grand_selectall', function () {
                    let isChecked = $(this).is(':checked');
                    $('.selectall, .permissioncheckbox').prop('checked', isChecked);
                });

                // Initialize checkboxes on page load
                initializeCheckBoxes();

                function initializeCheckBoxes() {
                    updateSelectAll();
                    updateGrandSelectAll();
                }

                // Update "select all" for each row based on individual checkboxes
                function updateSelectAll() {
                    $('.selectall').each(function () {
                        let allChecked = $(this).closest('tr').find('.permissioncheckbox').length ===
                            $(this).closest('tr').find('.permissioncheckbox:checked').length;
                        $(this).prop('checked', allChecked);
                    });
                }

                // Update the "grand select all" based on all "select all" checkboxes
                function updateGrandSelectAll() {
                    let allChecked = $('.selectall').length === $('.selectall:checked').length;
                    $('.grand_selectall').prop('checked', allChecked);
                }

                // Handle individual permission checkbox click
                $(".permissionTable").on('click', '.permissioncheckbox', function () {
                    let row = $(this).closest('tr');
                    let allChecked = row.find('.permissioncheckbox').length === row.find('.permissioncheckbox:checked').length;
                    row.find('.selectall').prop('checked', allChecked);
                    updateGrandSelectAll();
                });

            });

            function pleaseWaitSubmitButton(buttonId, labelId, message, mode) {
                let btn = document.getElementById(buttonId);
                let label = document.getElementById(labelId);

                if (mode === 1) { // during form submission
                    btn.disabled = true;
                    btn.innerHTML = message;
                    if (label) label.innerHTML = message;
                } else if (mode === 2) { // initial state
                    btn.disabled = false;
                    if (label) label.innerHTML = '';
                }
            }
        });
    </script>
@endpush

