@props([
    'tabs' => [],
    'active' => null,
])

@php($initial = $active ?? array_key_first($tabs))

<div {{ $attributes }} x-data="{ tab: @js($initial) }">
    <div class="flex gap-1 border-b border-outline dark:border-outline-dark" role="tablist">
        @foreach ($tabs as $key => $label)
            <button
                type="button"
                role="tab"
                class="cursor-pointer border-b-2 border-transparent px-3 py-2 text-sm text-on-surface transition hover:text-on-surface-strong dark:text-on-surface-dark dark:hover:text-on-surface-dark-strong"
                :class="tab === @js($key) && 'border-primary text-on-surface-strong dark:border-primary-dark dark:text-on-surface-dark-strong'"
                :aria-selected="tab === @js($key)"
                x-on:click="tab = @js($key)"
            >{{ $label }}</button>
        @endforeach
    </div>

    <div class="pt-4">{{ $slot }}</div>
</div>
