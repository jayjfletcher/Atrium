@props([
    'title',
    'description' => null,
    'icon' => null,
])

<div {{ $attributes->class('flex flex-col items-center justify-center px-4 py-12 text-center') }}>
    @if ($icon)
        <div class="mb-3 text-on-surface dark:text-on-surface-dark" aria-hidden="true">{!! $icon !!}</div>
    @endif

    <p class="text-sm font-medium text-on-surface-strong dark:text-on-surface-dark-strong">{{ $title }}</p>

    @if ($description)
        <p class="mt-1 text-sm text-on-surface dark:text-on-surface-dark">{{ $description }}</p>
    @endif

    @isset($actions)
        <div class="mt-4 flex items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
