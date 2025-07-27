@extends('layouts.backend.app')
@section('title', __('label.subtopic'))
@section('content')
    <div class="container-fluid">
        <div id="content">
            <div class="d-sm-flex align-items-center justify-content-end mb-4">
                <a href="{{ route('backend.subtopic.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50"></i> {{__('label.create')}}
                </a>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="subtopicsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{__('label.topic')}}</th>
                                    <th>{{__('label.subtopic')}}</th>
                                    <th>{{__('label.descriptions')}}</th>
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
        function confirmDelete(topicId) {
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
                    document.getElementById(`delete-subtopic-form-${topicId}`).submit();
                }
            });
        }

        $(document).ready(function() {
            $('#subtopicsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('backend.subtopic.get_tertiary_topic_for_dt') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'topic_name', name: 'topic_name', orderable: false, searchable: false },
                    { data: 'subtopic_name', name: 'subtopic_name', orderable: false, searchable: false },
                    { data: 'description', name: 'description' },
                    { data: 'status_badge', name: 'is_active', orderable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[5, 'desc']], // Order by created_at (6th column)
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                }
            });
        });
    </script>
@endpush
