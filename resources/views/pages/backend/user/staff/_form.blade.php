<form action="{{ $action }}" method="POST" id="staffForm">
    @csrf
    @isset($method) @method($method) @endisset

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">{{ __('name') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="email">{{ __('email') }} <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="phone">{{ __('phone') }} <span class="text-danger">*</span></label>
                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                       id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}" required>
                <input type="hidden" name="phone_country" id="phone_country">
                <small class="text-warning">Phone number must be registered on WhatsApp</small>
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="department">{{ __('department') }}</label>
                <input type="text" class="form-control"
                       id="department" name="department" value="{{ old('department', $user->department ?? '') }}">
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active"
                   name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">
                {{ __('Active') }}
            </label>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="role_id">{{ __('label.roles') }} <span class="text-danger">*</span></label>
                <select class="form-control select2" id="role_id" name="role_id[]" multiple required
                        data-placeholder="{{ __('label.select_roles') }}">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}"
                                @if(in_array($role->id, old('role_id', $userRoles ?? []))) selected @endif>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="topic_ids">{{ __('label.topic') }}</label>
                <select class="form-control select2-ajax" id="topic_ids" name="topic_ids[]" multiple
                        data-placeholder="{{ __('Search for topics of specialization...') }}"
                        data-ajax-url="{{ route('backend.topic.search') }}">
{{--                    @if(old('topic_ids', $userTopics ?? []))--}}
{{--                        @foreach(old('topic_ids', $userTopics ?? []) as $topic_id)--}}
{{--                            <option value="{{ $topic_id }}" selected>{{ $topic_id }}</option>--}}
{{--                        @endforeach--}}
{{--                    @endif--}}
                </select>
            </div>
        </div>
    </div>

    <div class="form-group mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ __('label.submit') }}
        </button>
        <a href="{{ route('backend.user.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> {{ __('label.cancel') }}
        </a>
    </div>
</form>
