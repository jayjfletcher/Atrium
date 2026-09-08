@props([
    'label',
    'value',
    'change' => null,
    'trend' => null,
    'icon' => null,
])

<div {{ $attributes->class('rounded-radius border border-outline bg-surface p-5 dark:border-outline-dark dark:bg-surface-dark') }}>
    @if ($icon)
        <span class="mb-2 inline-flex text-on-surface dark:text-on-surface-dark" aria-hidden="true">{!! $icon !!}</span>
    @endif

    <p class="text-sm text-on-surface dark:text-on-surface-dark">{{ $label }}</p>

    <p class="mt-1 text-2xl font-semibold tabular-nums text-on-surface-strong dark:text-on-surface-dark-strong">{{ $value }}</p>

    @if ($change !== null)
        <p @class([
            'mt-1 text-sm',
            'text-success' => $trend === 'up',
            'text-danger' => $trend === 'down',
            'text-on-surface dark:text-on-surface-dark' => ! in_array($trend, ['up', 'down'], true),
        ])>{{ $change }}</p>
    @endif
</div>
