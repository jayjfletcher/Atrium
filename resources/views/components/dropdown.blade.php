@props(['align' => 'left'])

<div {{ $attributes->class('relative inline-block') }} x-data="{ open: false }" x-on:click.outside="open = false">
    <div x-on:click="open = ! open">{{ $trigger ?? '' }}</div>

    <div
        @class([
            'absolute z-30 mt-2 min-w-52 rounded-radius border border-outline bg-surface p-1.5 text-on-surface shadow-lg dark:border-outline-dark dark:bg-surface-dark dark:text-on-surface-dark',
            'left-0' => $align === 'left',
            'right-0' => $align === 'right',
        ])
        x-show="open"
        x-cloak
    >
        {{ $slot }}
    </div>
</div>
