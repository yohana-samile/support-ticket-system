@extends('layouts.backend.app')
@section('title', 'Topic')
@section('content')
    <div class="container-fluid">
        <div id="content">
            <div class="d-sm-flex align-items-center justify-content-end mb-4">
                <a href="{{ route('backend.topic.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Create New Topic
                </a>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Saas_app</th>
                                    <th>Descriptions</th>
                                    <th>Status</th>
                                    <th>Created_at</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topics as $topic)
                                    <tr>
                                        <td>
                                            <a href="{{ route('backend.ticket.show', $topic->uid) }}">
                                                {{ Str::limit($topic->name, 30) }}
                                            </a>
                                        </td>
                                        <td>{{ $topic->saasApp->name ?? '' }}</td>
                                        <td>{{ Str::limit($topic->description, 30) }}</td>
                                        <td>
                                            @if ($topic->is_active)
                                                <span class="badge bg-primary text-white">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $topic->created_at->diffForHumans() }}</td>

                                        <td>
                                            <a href="{{ route('backend.topic.show', $topic->uid) }}" class="text-info mr-2 text-decoration-none" title="View">
                                                <i class="fas fa-eye fa-sm"></i>
                                            </a>

                                            <a href="{{ route('backend.topic.edit', $topic->uid) }}" class="text-primary mr-2 text-decoration-none" title="Edit">
                                                <i class="fas fa-edit fa-sm"></i>
                                            </a>
                                            @if($topic->can_be_deleted)
                                                <a href="javascript:void(0);" class="text-danger mr-2 text-decoration-none" title="Delete" onclick="confirmDelete('{{ $topic->uid }}')">
                                                    <i class="fas fa-trash fa-sm"></i>
                                                </a>

                                                <form id="delete-topic-form-{{ $topic->uid }}" action="{{ route('backend.topic.destroy', $topic->uid) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Title</th>
                                    <th>Saas_app</th>
                                    <th>Descriptions</th>
                                    <th>Status</th>
                                    <th>Created_at</th>
                                    <th>Actions</th>
                                </tr>
                            </tfoot>
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
                    document.getElementById(`delete-topic-form-${topicId}`).submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            $(document).ready(function() {
                $('#dataTable').DataTable({
                    "paging": false,
                    "searching": true,
                    "info": false,
                    "ordering": true,
                    "columnDefs": [
                        { "orderable": false, "targets": -1 } // Disable sorting for actions column
                    ]
                });
            });
        });
    </script>
@endpush
