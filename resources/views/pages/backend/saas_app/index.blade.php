@extends('layouts.backend.app')
@section('title', __('label.saas_app'))
@section('content')
    <div class="container-fluid">
        <div id="content">
            <div class="d-sm-flex align-items-center justify-content-end mb-4">
                <a href="{{ route('backend.saas_app.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50"></i> {{__('label.create')}}
                </a>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="saasAppTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{__('label.name')}}</th>
                                    <th>{{__('label.abbreviation')}}</th>
                                    <th>{{__('label.created_at')}}</th>
                                    <th>{{__('label.actions')}}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
@endpush

@push('scripts')
    <script>
        function confirmDelete(saasAppId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-saas_app-form-${saasAppId}`).submit();
                }
            });
        }

        $(document).ready(function() {
            $('#saasAppTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('backend.saas_app.get_saas_app_for_dt') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'name', name: 'name', orderable: true, searchable: true },
                    { data: 'abbreviation', name: 'abbreviation', orderable: true, searchable: true },
                    { data: 'created_at', name: 'created_at', orderable: true },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[3, 'desc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                }
            });
        });
    </script>
@endpush
