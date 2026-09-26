@props([
    'title',
    'description' => null,
    'icon' => null,
])

<div {{ $attributes->class('flex flex-col items-center justify-center rounded-radius border border-dashed border-outline px-6 py-14 text-center dark:border-outline-dark') }}>
    <div class="mb-4 grid size-11 place-items-center rounded-radius bg-on-surface-strong/5 text-on-surface ring-1 ring-on-surface-strong/5 dark:bg-white/5 dark:text-on-surface-dark dark:ring-white/10 [&_svg]:size-5" aria-hidden="true">
        @if ($icon)
            {!! $icon !!}
        @else
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
            </svg>
        @endif
    </div>

    <p class="text-sm font-medium text-on-surface-strong dark:text-on-surface-dark-strong">{{ $title }}</p>

    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-on-surface dark:text-on-surface-dark">{{ $description }}</p>
    @endif

    @isset($actions)
        <div class="mt-5 flex items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
