@extends('layouts.frontend.app')
@section('title', 'Create New Ticket')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Create New Ticket</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('frontend.ticket.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @include('pages.frontend.ticket.partials._form', [
                              'categories' => $categories,
                              'priorities' => $priorities,
                              'buttonText' => 'Create Ticket'
                          ])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .custom-file-label::after {
                content: "Browse";
            }
            .select2-container--default .select2-selection--single {
                height: calc(1.5em + 0.75rem + 2px);
                border: 1px solid #d1d3e2;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: calc(1.5em + 0.75rem + 2px);
            }
            .gap-3 {
                gap: 1rem;
            }
        </style>
    @endpush


    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            document.getElementById('description').style.height = document.getElementById('description').scrollHeight + 'px';
            document.addEventListener('DOMContentLoaded', function() {
                $(document).ready(function () {

                    $('.select2').select2({
                        placeholder: "Select an option",
                        allowClear: true,
                        width: '100%',
                    });

                    document.querySelector('.custom-file-input').addEventListener('change', function (e) {
                        var files = e.target.files;
                        var label = document.querySelector('.custom-file-label');
                        label.textContent = files.length > 1 ? files.length + ' files selected' : (files[0] ? files[0].name : 'Choose files');
                    });

                    if (!document.getElementById('due_date').value) {
                        const dueDateInput = document.getElementById('due_date');
                        if (dueDateInput && !dueDateInput.value) {
                            const now = new Date();
                            dueDateInput.value = now.toISOString().slice(0, 16);
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection

