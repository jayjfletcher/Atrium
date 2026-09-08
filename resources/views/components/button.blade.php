@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-radius font-medium tracking-wide transition text-center cursor-pointer hover:opacity-75 focus-visible:outline-2 focus-visible:outline-offset-2 active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed';

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-base',
    ];

    $variants = [
        'primary' => 'bg-primary border border-primary text-on-primary focus-visible:outline-primary dark:bg-primary-dark dark:border-primary-dark dark:text-on-primary-dark dark:focus-visible:outline-primary-dark',
        'secondary' => 'bg-secondary border border-secondary text-on-secondary focus-visible:outline-secondary dark:bg-secondary-dark dark:border-secondary-dark dark:text-on-secondary-dark dark:focus-visible:outline-secondary-dark',
        'outline' => 'bg-transparent border border-outline text-on-surface focus-visible:outline-outline dark:border-outline-dark dark:text-on-surface-dark dark:focus-visible:outline-outline-dark',
        'ghost' => 'bg-transparent text-on-surface focus-visible:outline-outline dark:text-on-surface-dark dark:focus-visible:outline-outline-dark',
        'danger' => 'bg-danger border border-danger text-on-danger focus-visible:outline-danger',
        'success' => 'bg-success border border-success text-on-success focus-visible:outline-success',
        'warning' => 'bg-warning border border-warning text-on-warning focus-visible:outline-warning',
        'info' => 'bg-info border border-info text-on-info focus-visible:outline-info',
    ];

    $classes = trim($base.' '.($sizes[$size] ?? $sizes['md']).' '.($variants[$variant] ?? $variants['primary']));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
