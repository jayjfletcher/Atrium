@props(['variant' => 'neutral'])

@php
    $variants = [
        'neutral' => 'bg-on-surface-strong/5 text-on-surface ring-on-surface-strong/10 dark:bg-white/5 dark:text-on-surface-dark dark:ring-white/10',
        'primary' => 'bg-primary/10 text-primary ring-primary/20 dark:bg-primary-dark/10 dark:text-primary-dark dark:ring-primary-dark/25',
        'success' => 'bg-success/10 text-success ring-success/20',
        'warning' => 'bg-warning/10 text-warning ring-warning/25',
        'danger' => 'bg-danger/10 text-danger ring-danger/20',
        'info' => 'bg-info/10 text-info ring-info/20',
    ];
@endphp

<span {{ $attributes->class('inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset '.($variants[$variant] ?? $variants['neutral'])) }}>
    {{ $slot }}
</span>
