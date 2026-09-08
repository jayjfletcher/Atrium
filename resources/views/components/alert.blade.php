@props([
    'variant' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $variants = [
        'info' => 'border-info/50 bg-info/10 text-info',
        'success' => 'border-success/50 bg-success/10 text-success',
        'warning' => 'border-warning/50 bg-warning/10 text-warning',
        'danger' => 'border-danger/50 bg-danger/10 text-danger',
    ];
@endphp

<div
    {{ $attributes->class('flex w-full items-start gap-3 rounded-radius border px-4 py-3 text-sm '.($variants[$variant] ?? $variants['info'])) }}
    role="alert"
    @if ($dismissible) x-data="{ shown: true }" x-show="shown" @endif
>
    <div class="flex-1">
        @if ($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif

        <div @class(['mt-1' => (bool) $title])>{{ $slot }}</div>
    </div>

    @if ($dismissible)
        <button type="button" class="shrink-0 cursor-pointer opacity-70 transition hover:opacity-100" x-on:click="shown = false" aria-label="Dismiss">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>
