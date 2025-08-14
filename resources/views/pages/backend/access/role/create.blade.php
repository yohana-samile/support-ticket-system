@extends('layouts.backend.app')
@section('title', __('label.add_role'))
@section('content')
    <div class="container-fluid">
        <div id="content">
            {!! Html::formOpen(['route' => 'backend.role.store', 'autocomplete' => 'off','method' => 'post', 'name' => 'create', 'class' => 'needs-validation', 'novalidate']) !!}
            {{ Html::hidden('action_type', 1) }}

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="form-group row mb-2">
                                <div class="col-lg-3 col-md-6">
                                    {{ Html::labels('display_name', __('label.name'), ['class' =>'required_asterik col-form-label']) }}
                                    {{ Html::texts('display_name', null, ['class'=>'form-control required', 'id' => 'name', 'autocomplete' => 'off']) }}
                                    {!! $errors->first('display_name', '<span class="badge badge-pill badge-danger">:message</span>') !!}
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    {{ Html::labels('description', __('label.descriptions'), ['class' =>'required_asterik col-form-label']) }}
                                    {{ Html::texts('description', null, ['class'=>'form-control required', 'id' => 'description', 'autocomplete' => 'off']) }}
                                    {!! $errors->first('description', '<span class="badge badge-pill badge-danger">:message</span>') !!}
                                </div>
                            </div>

                            <br>

                            {{ Html::labels('permission_id', __('label.permission'), ['class' =>'col-form-label']) }}
                            <table class="table table-bordered permissionTable bg-white my-4" style="width: 100%;">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{ __('label.group') }}</th>
                                    <th>
                                        <label>
                                            <input class="grand_selectall" type="checkbox">
                                            {{ __('Select All') }}
                                        </label>
                                    </th>
                                    <th>{{ __("Available permissions") }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($permissions as $groupId => $groupPermissions)
                                    <tr>
                                        <td>
                                            {{ $groupPermissions->first()->permissionGroup->name ?? 'No Group Assigned' }}
                                        </td>
                                        <td>
                                            <label>
                                                <input class="selectall" type="checkbox">
                                                {{ __('Select All') }}
                                            </label>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap">
                                                @forelse($groupPermissions as $permission)
                                                    <label class="mr-3 mb-2">
                                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="permissioncheckbox">
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
                                        <label class="form-check-label">
                                            {{ Html::checkbox('isactive', 1, null, ['class' => 'form-check-input', 'id' => 'isactive']) }}
                                            {{ __('label.is_active') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            {{ Html::checkbox('isadmin', 1, null, ['class' => 'form-check-input', 'id' => 'isadmin']) }}
                                            {{ __('label.is_admin') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <br/>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="float-right">
                                        {{ link_to_route('backend.role.index', trans('label.cancel'), [], ['id'=> 'cancel', 'class' => 'btn btn-secondary']) }}
                                        {{ Html::submits(trans('label.submit'), ['class' => 'btn btn-primary', 'id' => 'submit_btn']) }}
                                        <label id="submit_label"></label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {!! Html::formClose() !!}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            pleaseWaitSubmitButton("submit_btn","submit_label","{{ trans('label.please_wait') }}",2);
            $('body').on('submit', 'form[name=create]', function(e) {
                e.preventDefault();
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
