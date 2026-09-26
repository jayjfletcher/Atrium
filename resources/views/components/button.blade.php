@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-radius font-medium text-center cursor-pointer transition-[background-color,border-color,color,box-shadow] focus-visible:outline-2 focus-visible:outline-offset-2 active:translate-y-px disabled:pointer-events-none disabled:opacity-50 [&_svg]:size-4 [&_svg]:shrink-0';

    $sizes = [
        'sm' => 'h-8 px-3 text-xs',
        'md' => 'h-9 px-3.5 text-sm',
        'lg' => 'h-10 px-4 text-sm',
    ];

    $variants = [
        'primary' => 'bg-primary text-on-primary shadow-xs hover:bg-primary/90 focus-visible:outline-primary dark:bg-primary-dark dark:text-on-primary-dark dark:hover:bg-primary-dark/90 dark:focus-visible:outline-primary-dark',
        'secondary' => 'bg-secondary text-on-secondary shadow-xs hover:bg-secondary/85 focus-visible:outline-secondary dark:bg-secondary-dark dark:text-on-secondary-dark dark:hover:bg-secondary-dark/85 dark:focus-visible:outline-secondary-dark',
        'outline' => 'border border-outline bg-surface text-on-surface-strong shadow-xs hover:border-on-surface/25 hover:bg-surface-alt focus-visible:outline-primary dark:border-outline-dark dark:bg-white/5 dark:text-on-surface-dark-strong dark:hover:border-white/20 dark:hover:bg-white/10 dark:focus-visible:outline-primary-dark',
        'ghost' => 'text-on-surface hover:bg-on-surface-strong/5 hover:text-on-surface-strong focus-visible:outline-primary dark:text-on-surface-dark dark:hover:bg-white/5 dark:hover:text-on-surface-dark-strong dark:focus-visible:outline-primary-dark',
        'danger' => 'bg-danger text-on-danger shadow-xs hover:bg-danger/90 focus-visible:outline-danger',
        'success' => 'bg-success text-on-success shadow-xs hover:bg-success/90 focus-visible:outline-success',
        'warning' => 'bg-warning text-on-warning shadow-xs hover:bg-warning/90 focus-visible:outline-warning',
        'info' => 'bg-info text-on-info shadow-xs hover:bg-info/90 focus-visible:outline-info',
    ];

    $classes = trim($base.' '.($sizes[$size] ?? $sizes['md']).' '.($variants[$variant] ?? $variants['primary']));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
