@extends('layouts.backend.app')
@section('title', __('label.mno'))
@section('content')
    <div class="container-fluid">
        <div id="content">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="mnoTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{__('label.name')}}</th>
                                    <th>{{__('label.status')}}</th>
                                    <th>{{__('label.created_at')}}</th>
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

        document.addEventListener('DOMContentLoaded', function() {
            $(document).ready(function() {
                $('#mnoTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('backend.operator.get_operator_for_dt') }}",
                        type: 'GET'
                    },
                    columns: [
                        { data: 'name', name: 'name', orderable: false, searchable: true },
                        { data: 'status_badge', name: 'is_active', orderable: true },
                        { data: 'created_at', name: 'created_at' },
                    ],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search...",
                    }
                });
            });
        });
    </script>
@endpush
