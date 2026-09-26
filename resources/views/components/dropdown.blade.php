@props(['align' => 'left'])

<div {{ $attributes->class('relative inline-block') }} x-data="{ open: false }" x-on:click.outside="open = false" x-on:keydown.escape="open = false">
    <div x-on:click="open = ! open">{{ $trigger ?? '' }}</div>

    <div
        @class([
            'absolute z-30 mt-2 min-w-52 rounded-radius border border-outline bg-surface p-1 text-on-surface shadow-xl dark:border-outline-dark dark:bg-surface-dark dark:text-on-surface-dark',
            'left-0 origin-top-left' => $align === 'left',
            'right-0 origin-top-right' => $align === 'right',
        ])
        x-show="open"
        x-cloak
        x-transition:enter="transition duration-100 ease-out"
        x-transition:enter-start="scale-95 opacity-0"
        x-transition:leave="transition duration-75 ease-in"
        x-transition:leave-end="scale-95 opacity-0"
    >
        {{ $slot }}
    </div>
</div>
