@props([
    'text',
    'position' => 'top',
])

@php
    $positions = [
        'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-2',
        'left' => 'right-full top-1/2 -translate-y-1/2 mr-2',
        'right' => 'left-full top-1/2 -translate-y-1/2 ml-2',
    ];
@endphp

<span {{ $attributes->class('relative inline-flex') }} x-data="{ show: false }"
      x-on:mouseenter="show = true" x-on:mouseleave="show = false"
      x-on:focusin="show = true" x-on:focusout="show = false">
    {{ $slot }}

    <span class="pointer-events-none absolute z-40 whitespace-nowrap rounded-md bg-on-surface-strong px-2 py-1 text-xs font-medium text-surface shadow-lg dark:bg-on-surface-dark-strong dark:text-surface-dark {{ $positions[$position] ?? $positions['top'] }}"
          x-show="show" x-cloak x-transition.opacity.duration.100ms role="tooltip">{{ $text }}</span>
</span>
