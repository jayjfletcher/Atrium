@props(['variant' => 'neutral'])

@php
    $variants = [
        'neutral' => 'bg-surface-alt text-on-surface dark:bg-surface-dark-alt dark:text-on-surface-dark',
        'primary' => 'bg-primary/10 text-primary dark:bg-primary-dark/10 dark:text-primary-dark',
        'success' => 'bg-success/10 text-success',
        'warning' => 'bg-warning/10 text-warning',
        'danger' => 'bg-danger/10 text-danger',
        'info' => 'bg-info/10 text-info',
    ];
@endphp

<span {{ $attributes->class('inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium '.($variants[$variant] ?? $variants['neutral'])) }}>
    {{ $slot }}
</span>
