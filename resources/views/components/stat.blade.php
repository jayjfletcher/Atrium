@props([
    'label',
    'value',
    'change' => null,
    'trend' => null,
    'icon' => null,
])

<div {{ $attributes->class('rounded-radius border border-outline bg-surface p-5 shadow-xs dark:border-outline-dark dark:bg-surface-dark') }}>
    <div class="flex items-start justify-between gap-3">
        <p class="text-sm font-medium text-on-surface dark:text-on-surface-dark">{{ $label }}</p>

        @if ($icon)
            <span class="grid size-8 shrink-0 place-items-center rounded-radius bg-primary/10 text-primary dark:bg-primary-dark/15 dark:text-primary-dark [&_svg]:size-4" aria-hidden="true">{!! $icon !!}</span>
        @endif
    </div>

    <p class="mt-2 text-3xl font-semibold tracking-tight tabular-nums text-on-surface-strong dark:text-on-surface-dark-strong">{{ $value }}</p>

    @if ($change !== null)
        <p @class([
            'mt-3 inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 text-xs font-medium tabular-nums',
            'bg-success/10 text-success' => $trend === 'up',
            'bg-danger/10 text-danger' => $trend === 'down',
            'bg-on-surface-strong/5 text-on-surface dark:bg-white/5 dark:text-on-surface-dark' => ! in_array($trend, ['up', 'down'], true),
        ])>
            @if ($trend === 'up')
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-3" aria-hidden="true"><path fill-rule="evenodd" d="M8 14a.75.75 0 0 1-.75-.75V4.56L4.03 7.78a.75.75 0 0 1-1.06-1.06l4.5-4.5a.75.75 0 0 1 1.06 0l4.5 4.5a.75.75 0 0 1-1.06 1.06L8.75 4.56v8.69A.75.75 0 0 1 8 14Z" clip-rule="evenodd" /></svg>
            @elseif ($trend === 'down')
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-3" aria-hidden="true"><path fill-rule="evenodd" d="M8 2a.75.75 0 0 1 .75.75v8.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-4.5 4.5a.75.75 0 0 1-1.06 0l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.22 3.22V2.75A.75.75 0 0 1 8 2Z" clip-rule="evenodd" /></svg>
            @endif
            {{ $change }}
        </p>
    @endif
</div>
