@props([
    'name',
    'title' => null,
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-lg',
        'lg' => 'max-w-3xl',
        'xl' => 'max-w-5xl',
    ];
@endphp

<div
    x-data="{ open: false }"
    x-on:atrium-modal-open.window="if ($event.detail === '{{ $name }}') open = true"
    x-on:atrium-modal-close.window="if ($event.detail === '{{ $name }}') open = false"
    x-on:keydown.escape.window="open = false"
>
    <div class="fixed inset-0 z-40 bg-black/50" x-show="open" x-cloak x-on:click="open = false"></div>

    <div
        {{ $attributes->class('fixed left-1/2 top-[10vh] z-50 w-[calc(100vw-2rem)] -translate-x-1/2 overflow-y-auto rounded-radius border border-outline bg-surface text-on-surface shadow-lg dark:border-outline-dark dark:bg-surface-dark dark:text-on-surface-dark '.($sizes[$size] ?? $sizes['md'])) }}
        style="max-height: 80vh"
        x-show="open"
        x-cloak
        role="dialog"
        aria-modal="true"
    >
        @if ($title)
            <div class="flex items-center justify-between border-b border-outline px-5 py-3 dark:border-outline-dark">
                <h2 class="text-base font-semibold text-on-surface-strong dark:text-on-surface-dark-strong">{{ $title }}</h2>

                <button type="button" class="cursor-pointer opacity-70 transition hover:opacity-100" x-on:click="open = false" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <div class="p-5">{{ $slot }}</div>

        @isset($footer)
            <div class="border-t border-outline px-5 py-3 dark:border-outline-dark">{{ $footer }}</div>
        @endisset
    </div>
</div>
