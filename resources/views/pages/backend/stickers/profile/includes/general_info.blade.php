<div class="row">
    <!-- General Info -->
    <div class="col-md-7">
        <div class="card mb-3">
            <div class="card-header bg-light text-muted">
                <strong>{{ __('label.general_info') }}</strong>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <th>@lang('label.note')</th>
                            <td> {!! Purifier::clean($sticker->note) !!} </td>
                        </tr>
                        <tr>
                            <th>@lang('label.status')</th>
                            <td>{{ $sticker->status }}</td>
                        </tr>
                        <tr>
                            <th>@lang('label.is_private')</th>
                            <td>
                                @if($sticker->is_private === true)
                                    <span class="badge bg-primary text-white">{{ config('constants.options.yes') }}</span>
                                @else
                                    <span class="badge bg-danger text-white">{{ config('constants.options.no') }}</span>
                                @endif
                            </td>
                        </tr>
                        @if($sticker->remind_at)
                            <tr>
                                <th>@lang('label.remind_at')</th>
                                <td>{{ $sticker->remind_at }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>@lang('label.created_at')</th>
                            <td>{{ $sticker->created_at }}</td>
                        </tr>

                        @if(!$sticker->is_private)
                            <tr>
                                <th>@lang('label.topic_of_specialization')</th>
                                <td>
                                    @if($sticker->recipients->count())
                                        <span class="badge badge-info mb-3">
                                            Shared with:
                                            {{ $sticker->recipients->pluck('name')->join(', ') }}
                                        </span>
                                    @else
                                        <span class="text-muted">No recipients</span>
                                    @endif
                                </td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar Summary -->
    <div class="col-md-5">
        <div class="card mb-3">
            <div class="card-header bg-light text-muted">
                <strong>{{ __('label.sidebar_summary') }}</strong>
            </div>
            <div class="card-body p-0">
                <div class="container py-4">
                    <div class="card" style="background-color: {{ $sticker->color_code ?? '#ffffff' }}; border: none;">
                        <div class="card-body" style="background-color: rgba(255,255,255,0.85); border-radius: 0.25rem; margin: 10px;">
                            <!-- Privacy Badge -->
                            @if($sticker->is_private === true)
                                <span class="badge badge-danger mb-3">{{__('label.private')}}</span>
                            @else
                                <span class="badge badge-success mb-3">{{__('label.public')}}</span>
                            @endif

                            <!-- Note Content -->
                            <div class="p-3 mb-3 rounded" style="background-color: white;">
                                {!! Purifier::clean($sticker->note) !!}
                            </div>

                            <!-- Reminder Date -->
                            @if($sticker->remind_at)
                                <div class="alert alert-light mt-3 mb-0">
                                    <i class="far fa-bell"></i>
                                    Reminder set for: {{ \Carbon\Carbon::parse($sticker->remind_at)->format('M j, Y \a\t g:i A') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
