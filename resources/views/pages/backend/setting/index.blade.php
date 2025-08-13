@extends('layouts.backend.app')
@section('title', __('label.setting'))
@section('content')
    <div class="container-fluid">
        <div id="content">
            <div class="card">
                <div class="card-header bg-light text-dark">
                    <h5 class="mb-0">{{ __('label.setting') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.setting.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <h6 class="text-primary mb-3">{{ __('label.sticker_settings') }}</h6>

                            <!-- Visibility Toggle -->
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" value="1" class="custom-control-input" id="stickerButtonVisible" name="show_customizer_button"
                                        {{ $settings->show_customizer_button ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="stickerButtonVisible">
                                        {{ __('label.show_quick_sticker_button') }}
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    {{ __('label.toggle_sticker_button') }}
                                </small>
                            </div>

                            <!-- Theme Selection -->
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="theme" value="0">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           id="theme"
                                           name="theme"
                                           value="1"
                                        {{ $settings->theme === \App\Constants\NotificationConstants::THEME_DARK ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="theme">
                                        {{ __('label.switch_dark_mode') }}
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    {{ __('label.switch_light_dark_mode') }}
                                </small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-primary mb-3">{{ __('label.notification_settings') }}</h6>

                            <!-- Notification Channel -->
                            <div class="form-group">
                                <label for="notificationChannel">{{ __('label.notification_channel') }}</label>
                                <select name="notification_channel" class="form-control">
                                    @foreach(\App\Constants\NotificationConstants::getAllChannels() as $channel)
                                        <option value="{{ $channel }}" {{ $settings->notification_channel === $channel ? 'selected' : '' }}>
                                            {{ __('label.notification_channel_' . $channel) }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    {{ __('label.sticker_notification_preference') }}
                                </small>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('label.submit') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: calc(1.5em + .75rem + 2px);
            border: 1px solid #ced4da;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                minimumResultsForSearch: Infinity
            });
        });
    </script>
@endpush
