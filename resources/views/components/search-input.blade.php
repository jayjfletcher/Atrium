@props([
    'name' => null,
    'value' => null,
    'placeholder' => null,
    'wrapper' => null,
])

{{-- A search field with a magnifier, for filtering lists in place or by GET. --}}
<div @class(['relative', $wrapper ?? 'w-full max-w-sm'])>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 opacity-60" aria-hidden="true">
        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
    </svg>
    <input
        type="search"
        @if ($name) name="{{ $name }}" value="{{ old($name, $value) }}" @endif
        @if ($placeholder) placeholder="{{ $placeholder }}" aria-label="{{ $placeholder }}" @endif
        {{ $attributes->class('h-9 w-full rounded-radius border border-outline bg-surface pl-8 pr-3 text-sm text-on-surface-strong shadow-xs transition placeholder:text-on-surface/60 hover:border-on-surface/30 focus:border-primary focus:outline-none focus:ring-3 focus:ring-primary/15 dark:border-outline-dark dark:bg-white/5 dark:text-on-surface-dark-strong dark:placeholder:text-on-surface-dark/60 dark:hover:border-white/20 dark:focus:border-primary-dark dark:focus:ring-primary-dark/20') }}
    />
</div>
