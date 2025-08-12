@extends('layouts.backend.app')
@section('title', __('label.sticker_profile'))
@section('content')
    <div class="card">
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs nav-border-top nav-border-top-primary mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium active" data-bs-toggle="tab" href="#general_tab" role="tab" aria-selected="true">
                        General
                    </a>
                </li>

            </ul>
            <div class="tab-content text-muted">
                <div class="tab-pane active show" id="general_tab" role="tabpanel">

                    {{--action section--}}
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-end flex-wrap">
                                <a href="{{ route('backend.stickers.edit', $sticker->uid) }}" class="btn btn-sm btn-primary mr-2 mb-2">
                                    <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> {{ __('label.edit') }}
                                </a>
                                <form id="delete-sticker-{{ $sticker->uid }}" action="{{ route('backend.stickers.destroy', $sticker->uid) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button" class="btn btn-sm btn-danger mr-2 mb-2" onclick="confirmDelete('{{ $sticker->uid }}')">
                                    <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> {{ __('label.delete') }}
                                </button>

                                <a href="{{ route('backend.stickers.index') }}" class="btn btn-sm btn-dark mb-2">
                                    <i class="ri-close align-bottom me-2 text-muted"></i> {{ __('label.close') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    @include('pages.backend.stickers.profile.includes.general_info')
                </div>

                {{--document tab--}}
                <div class="tab-pane" id="document_center_tab" role="tabpanel">
                    <div class="d-flex">

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


@push('styles')
    <style>

    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[style*="background-color"]').forEach(card => {
                const bgColor = getComputedStyle(card).backgroundColor;
                const rgb = bgColor.match(/\d+/g);

                if (rgb) {
                    const brightness = (parseInt(rgb[0]) * 299 +
                        parseInt(rgb[1]) * 587 +
                        parseInt(rgb[2]) * 114) / 1000;

                    if (brightness < 128) {
                        card.classList.add('dark-card');
                    }
                }
            });
        });

        function confirmDelete(uid) {
            Swal.fire({
                title: "{{ __('label.warning') }}",
                text: "{{ __('label.delete') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: "{{ trans('label.confirm') }}",
                cancelButtonText: "{{ trans('label.cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-sticker-' + uid).submit();
                }
            });
        }
    </script>
@endpush
