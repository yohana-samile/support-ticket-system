@extends('layouts.backend.app')
@section('title', __('label.role_preview'))
@section('content')
    <div class="container-fluid">
        <div id="content">
            <div class="card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-border-top nav-border-top-primary mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link font-weight-medium active" data-toggle="tab" href="#general_tab" role="tab" aria-selected="true">
                                {{ __('label.general') }}
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content text-muted">
                        <div class="tab-pane fade show active" id="general_tab" role="tabpanel">

                            {{-- action section --}}
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="float-right">
                                        @if(access()->allow('manage_role_and_permissions'))
                                            <a href="{{ route('backend.role.edit', $role->uid) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="ri-pencil-fill mr-2 text-muted"></i> {{ __('label.edit') }}
                                            </a>
                                            @if($role->checkIfCanBeDeleted())
                                                {!! link_to_route(
                                                    'backend.role.delete',
                                                    trans('label.crud.delete'),
                                                    [$role->uid],
                                                    [
                                                        'data-method' => 'delete',
                                                        'data-trans-button-cancel' => trans('buttons.general.cancel'),
                                                        'data-trans-button-confirm' => trans('buttons.general.confirm'),
                                                        'data-trans-title' => trans('label.warning'),
                                                        'data-trans-text' => trans('alert.delete'),
                                                        'class' => 'btn btn-danger btn-sm'
                                                    ]
                                                ) !!}
                                            @endif
                                        @endif
                                        <a href="{{ route('backend.role.index') }}" class="btn btn-sm btn-dark">
                                            <i class="ri-close mr-2 text-muted"></i> {{ __('label.close') }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            @include('pages.backend.access.role.profile.includes.general_info')

                        </div>

                        {{-- document tab --}}
                        <div class="tab-pane fade" id="document_center_tab" role="tabpanel">
                            <div class="d-flex">
                                {{-- Content for document tab --}}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
    </script>
@endpush
