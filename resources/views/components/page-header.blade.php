@props([
    'title',
    'description' => null,
])

<div {{ $attributes->class('flex flex-wrap items-start justify-between gap-3') }}>
    <div>
        <h1 class="text-xl font-semibold text-on-surface-strong dark:text-on-surface-dark-strong">{{ $title }}</h1>

        @if ($description)
            <p class="mt-1 text-sm text-on-surface dark:text-on-surface-dark">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
