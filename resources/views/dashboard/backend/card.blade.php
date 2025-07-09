@props(['color', 'icon', 'title', 'value'])
@php
    $colors = [
        'red' => ['bg' => 'red-100', 'text' => 'red-600'],
        'blue' => ['bg' => 'blue-100', 'text' => 'blue-600'],
        'yellow' => ['bg' => 'yellow-100', 'text' => 'yellow-600'],
        'green' => ['bg' => 'green-100', 'text' => 'green-600'],
    ];

    $icons = [
        'exclamation' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'users' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'check-circle' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
    ];
@endphp

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center">
        <div class="p-3 rounded-full bg-{{ $colors[$color]['bg'] }} text-{{ $colors[$color]['text'] }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$icon] }}" />
            </svg>
        </div>
        <div class="ml-4">
            <h3 class="text-gray-500 text-sm font-medium">{{ $title }}</h3>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($value) }}</p>
        </div>
    </div>
</div>
