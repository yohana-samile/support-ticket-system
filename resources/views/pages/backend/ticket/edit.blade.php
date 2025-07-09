@extends('layouts.backend.app')
@section('title', 'Edit Ticket - ' . $ticket->ticket_number)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">@yield('title')</h6>
                        <a href="{{ route('backend.ticket.show', $ticket->uid) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye mr-1"></i> View Ticket
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('backend.ticket.update', $ticket->uid) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            @include('pages.backend.ticket._form', [
                                'ticket' => $ticket,
                                'categories' => $categories,
                                'users' => $users,
                                'customers' => $customers,
                                'buttonText' => 'Update Ticket'
                            ])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pages.backend.ticket._delete_attachment_modal')
@endsection

@push('scripts')
    <script>
        const destroyAttachmentRoute = "{{ route('backend.attachment.destroy', ['attachment' => ':id']) }}";
        document.querySelectorAll('.delete-attachment').forEach(button => {
            button.addEventListener('click', function () {
                const attachmentId = this.getAttribute('data-attachment-id');
                const form = document.getElementById('deleteAttachmentForm');
                form.action = destroyAttachmentRoute.replace(':id', attachmentId);
                $('#deleteAttachmentModal').modal('show');
            });
        });
    </script>
@endpush
