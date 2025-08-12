@extends('layouts.backend.app')
@section('title', __('label.stickers'))
@section('content')
    <div class="container-fluid">
        <div id="content">
            <div class="d-sm-flex align-items-center justify-content-end mb-4">
                <a href="{{ route('backend.stickers.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50"></i> {{__('label.create')}}
                </a>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="stickersTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{__('label.note')}}</th>
                                    <th>{{__('label.status')}}</th>
                                    <th>{{__('label.created_at')}}</th>
                                    <th>{{__('label.is_private')}}</th>
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
        function confirmDelete(stickers) {
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
                    document.getElementById(`delete-stickers-form-${stickers}`).submit();
                }
            });
        }

        $(document).ready(function() {
            $('#stickersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('backend.stickers.get_stickers_for_dt') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'note', name: 'note', orderable: true, searchable: true },
                    { data: 'status_badge', name: 'status_badge', orderable: true, searchable: true },
                    { data: 'created_at', name: 'created_at', orderable: true },
                    { data: 'private_badge', name: 'private_badge', orderable: true },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[4, 'desc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search stickers...",
                }
            });
        });
    </script>
@endpush
