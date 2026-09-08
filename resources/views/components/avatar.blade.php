@props([
    'src' => null,
    'alt' => '',
    'initials' => null,
    'size' => 'md',
])

@php
    $sizes = ['xs' => 'size-6 text-xs', 'sm' => 'size-8 text-xs', 'md' => 'size-10 text-sm', 'lg' => 'size-12 text-base', 'xl' => 'size-16 text-lg'];
    $dimension = $sizes[$size] ?? $sizes['md'];
@endphp

@if ($src)
    <img src="{{ $src }}" alt="{{ $alt }}" {{ $attributes->class('shrink-0 rounded-full object-cover '.$dimension) }} />
@else
    <span {{ $attributes->class('inline-flex shrink-0 items-center justify-center rounded-full bg-surface-alt font-medium text-on-surface-strong dark:bg-surface-dark-alt dark:text-on-surface-dark-strong '.$dimension) }}>
        {{ $initials ?? $slot }}
    </span>
@endif
