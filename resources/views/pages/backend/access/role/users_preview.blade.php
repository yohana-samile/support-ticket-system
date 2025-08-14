@if($users->count() > 0)
    <ul class="list-unstyled mb-0">
        @foreach($users as $user)
            <li class="py-1">{{ $user->name }} ({{ $user->email }})</li>
        @endforeach
    </ul>
    @if($users->count() === 5)
        <p class="text-muted mb-0">And more...</p>
    @endif
@else
    <p class="text-muted mb-0">No users assigned</p>
@endif

