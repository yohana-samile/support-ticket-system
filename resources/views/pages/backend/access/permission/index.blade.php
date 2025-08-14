@extends('layouts.backend.main', ['title' => __('label.administrator.system.access_control.roles_permissions') , 'header' => __('label.administrator.system.access_control.roles_permissions')])

@include('includes.assets.datatable_assets')

@push('after-styles')
    <style>
    </style>
@endpush

@section('content')
    <section class="card mb-4">
        {{--Start: Datatable--}}
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-hover table-responsive-md" id="permission-table">
                        <thead>
                            <tr>
                                <th>@lang('label.sn')</th>
                                <th>{{ __('label.name') }}</th>
                                <th>{{ __('label.descriptions') }}</th>
                                <th>{{ __('label.standard.is_active') }}</th>
                                <th>{{ __('label.is_admin') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('after-scripts')
    <script type="text/javascript">
        var url = "{{ url("/") }}";

        $('#permission-table').DataTable({
            ajax: {
                url: '{{ route('admin.permission.get_all_for_dt') }}',
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'display_name', name: 'permissions.display_name', orderable: true, searchable: true },
                { data: 'description', name: 'permissions.description', orderable: true, searchable: true },
                { data: 'isactive', name: 'permissions.isactive', orderable: false, searchable: false },
                { data: 'isadmin', name: 'permissions.isadmin', orderable: false, searchable: false },
            ],
        });
    </script>
@endpush
