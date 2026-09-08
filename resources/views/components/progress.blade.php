@props([
    'value' => 0,
    'max' => 100,
    'label' => null,
])

@php($percent = $max > 0 ? min(100, max(0, ($value / $max) * 100)) : 0)

<div {{ $attributes->class('flex w-full flex-col gap-1') }}>
    @if ($label)
        <div class="flex items-center justify-between text-sm text-on-surface dark:text-on-surface-dark">
            <span>{{ $label }}</span>
            <span class="tabular-nums">{{ round($percent) }}%</span>
        </div>
    @endif

    <div class="h-2 w-full overflow-hidden rounded-full bg-surface-alt dark:bg-surface-dark-alt"
         role="progressbar" aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="{{ $max }}">
        <div class="h-full rounded-full bg-primary transition-all dark:bg-primary-dark" style="width: {{ $percent }}%"></div>
    </div>
</div>
