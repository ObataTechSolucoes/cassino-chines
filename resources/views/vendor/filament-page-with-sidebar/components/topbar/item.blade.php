@props([
    'active' => false,
    'activeIcon' => null,
    'badge' => null,
    'badgeColor' => null,
    'icon' => null,
    'shouldOpenUrlInNewTab' => false,
    'url' => null,
])

@php
    $tag = $url ? 'a' : 'button';
@endphp

<li
        @class([
            'fi-topbar-item-active' => $active,
        ])
>
    <{{ $tag }}
        @if ($url)
            {{ \Filament\Support\generate_href_html($url, $shouldOpenUrlInNewTab) }}
        @else
        type="button"
    @endif
    @class([
        'flex items-center justify-center gap-x-2 rounded-lg px-3 py-2 text-sm font-semibold outline-none transition duration-75 hover:bg-gray-50 focus:bg-gray-50 dark:hover:bg-white/5 dark:focus:bg-white/5',
        'text-gray-700 dark:text-gray-200' => ! $active,
        'bg-gray-50 text-primary-600 dark:bg-white/5 dark:text-primary-400' => $active,
    ])
    >

    <span class="whitespace-nowrap">
        {{ $slot }}
    </span>

    @if (filled($badge))
        <x-filament::badge :color="$badgeColor" size="sm">
            {{ $badge }}
        </x-filament::badge>
    @endif
</{{ $tag }}>
</li>
