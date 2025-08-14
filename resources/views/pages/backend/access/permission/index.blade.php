@extends('layouts.backend.app')
@section('title', __('label.permission'))
@section('content')
    <div class="container-fluid">
        <div id="content">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="permission-table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>@lang('label.sn')</th>
                                    <th>{{ __('label.name') }}</th>
                                    <th>{{ __('label.descriptions') }}</th>
                                    <th>{{ __('label.is_active') }}</th>
                                    <th>{{ __('label.is_admin') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var url = "{{ url("/") }}";

        $('#permission-table').DataTable({
            ajax: {
                url: '{{ route('backend.permission.get_all_for_dt') }}',
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
