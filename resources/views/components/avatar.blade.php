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
    <img src="{{ $src }}" alt="{{ $alt }}" {{ $attributes->class('shrink-0 rounded-full object-cover ring-1 ring-on-surface-strong/10 dark:ring-white/10 '.$dimension) }} />
@else
    <span {{ $attributes->class('inline-flex shrink-0 items-center justify-center rounded-full bg-primary/10 font-semibold text-primary ring-1 ring-primary/15 dark:bg-primary-dark/15 dark:text-primary-dark dark:ring-primary-dark/20 dark:text-on-surface-dark-strong '.$dimension) }}>
        {{ $initials ?? $slot }}
    </span>
@endif
