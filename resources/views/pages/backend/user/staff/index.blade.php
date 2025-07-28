@extends('layouts.backend.app')
@section('title', __('label.create'))
@section('content')
    <div class="container-fluid">
        <div id="content">
            <div class="d-sm-flex align-items-center justify-content-end mb-4">
                <a href="{{ route('backend.user.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" hidden>
                    <i class="fas fa-plus fa-sm text-white-50"></i> {{__('label.create')}}
                </a>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="staffUserTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{__('label.name')}}</th>
                                    <th>{{__('label.email')}}</th>
                                    <th>{{__('label.phone')}}</th>
                                    <th>{{__('label.is_manager')}}</th>
                                    <th>{{__('label.status')}}</th>
                                    <th>{{__('label.created_at')}}</th>
                                    <th>{{__('label.action')}}</th>
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
        function confirmDelete(client) {
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
                    document.getElementById(`delete-user-form-${client}`).submit();
                }
            });
        }

        $(document).ready(function() {
            $('#staffUserTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('backend.user.get_staff_user_for_dt') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'name', name: 'name', orderable: true, searchable: true },
                    { data: 'phone', name: 'phone', orderable: true, searchable: true },
                    { data: 'email', name: 'email', orderable: true, searchable: true },
                    { data: 'manager_badge', name: 'is_super_admin', orderable: false },
                    { data: 'status_badge', name: 'is_active', orderable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[4, 'desc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                }
            });
        });
    </script>
@endpush
