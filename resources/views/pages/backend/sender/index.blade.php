@extends('layouts.backend.app')
@section('title', __('label.sender_id'))
@section('content')
    <div class="container-fluid">
        <div id="content">
            @if(access()->allow('manage_sender_ids'))
                <div class="d-sm-flex align-items-center justify-content-end mb-4">
                    <a href="{{ route('backend.sender_id.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                        <i class="fas fa-plus fa-sm text-white-50"></i> {{__('label.create')}}
                    </a>
                </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="senderTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{__('label.name')}}</th>
                                    <th>{{__('label.status')}}</th>
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
        function confirmDelete(senderId) {
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
                    document.getElementById(`delete-sender_id-form-${senderId}`).submit();
                }
            });
        }

        $(document).ready(function() {
            $('#senderTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('backend.sender_id.get_sender_ids_for_dt') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'name', name: 'name', orderable: true, searchable: true },
                    { data: 'status_badge', name: 'status_badge', orderable: true, searchable: true },
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
