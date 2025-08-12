@php
    $oldTarget = old('target_user_id');

    if (is_array($oldTarget)) {
        $selected = $oldTarget;
    } elseif (!is_null($oldTarget)) {
        $selected = [(string)$oldTarget];
    } elseif (isset($sticker)) {
        if (!empty($sticker->is_for_all)) {
            $selected = ['all'];
        } else {
            $selected = $sticker->recipients->pluck('id')->map(function($v){ return (string)$v; })->toArray();
        }
    } else {
        $selected = [];
    }
@endphp

<form id="stickerForm" method="POST"
      action="{{ isset($sticker) ? route('backend.stickers.update', $sticker->uid) : route('backend.stickers.store') }}">
    @csrf
    @if(isset($sticker))
        @method('PUT')
    @endif

    <div class="modal-body">
        <!-- Note -->
        <div class="form-group">
            <label for="stickerNote">{{__('label.note')}} <span class="text-danger">*</span></label>
            <textarea
                class="form-control"
                id="stickerNote"
                name="note"
                rows="3"
                placeholder="Write your note here...">{{ old('note', $sticker->note ?? '') }}</textarea>
        </div>

        <!-- Reminder Date & Time -->
        <div class="form-group">
            <label for="reminder">Reminder Date & Time</label>
            <input
                type="datetime-local"
                class="form-control"
                id="reminder"
                name="remind_at"
                value="{{ old('remind_at', isset($sticker->remind_at) ? \Carbon\Carbon::parse($sticker->remind_at)->format('Y-m-d\TH:i') : '') }}">
        </div>

        <!-- Color -->
        <div class="form-group">
            <label for="stickerColor">{{__('label.sticker_color')}}</label>
            <input
                type="color"
                class="form-control"
                id="stickerColor"
                name="color_code"
                value="{{ old('color_code', $sticker->color_code ?? '#FFD700') }}">
        </div>

        <!-- Private Checkbox -->
        <div class="form-group form-check">
            <input
                type="checkbox"
                value="1"
                class="form-check-input"
                id="privateNote"
                name="is_private"
                {{ old('is_private', $sticker->is_private ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="privateNote">Private</label>
        </div>

        <!-- Recipient Select -->
        <div class="form-group" id="recipientGroup" style="{{ old('is_private', $sticker->is_private ?? true) ? 'display: none;' : '' }}">
            <label for="recipient">Send To</label>
            <select class="form-control select2" id="recipient" name="target_user_id">
                <option hidden disabled {{ count($selected) === 0 ? 'selected' : '' }}>
                    {{ __('label.select_user') }}
                </option>

                <option value="all" {{ in_array('all', $selected) ? 'selected' : '' }}>
                    {{ __('label.all_user') }}
                </option>

                @foreach($users ?? [] as $user)
                    <option value="{{ $user->id }}" {{ in_array((string)$user->id, $selected) ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary text-white">
        <i class="fas fa-save"></i> {{__('label.submit')}}
    </button>
    <a href="{{ route('backend.stickers.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> {{__('label.cancel')}}
    </a>
</form>
