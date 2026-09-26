@props([
    'wrapper' => null,
    'label' => null,
    'name',
    'type' => 'text',
    'value' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
])

@php
    $id = $attributes->get('id', 'atrium-'.$name);
    $resolvedError = $error ?? ($errors ?? null)?->first($name);

    // An input backed by a <datalist> is a combobox, so it wears the same
    // chevron as a select instead of the browser's own picker indicator.
    $hasList = $attributes->has('list');
@endphp

<div @class(['flex flex-col gap-1.5 text-on-surface dark:text-on-surface-dark', $wrapper ?? 'w-full'])>
    @if ($label)
        <label for="{{ $id }}" class="w-fit text-sm font-medium text-on-surface-strong dark:text-on-surface-dark-strong">
            {{ $label }}
            @if ($required)
                <span class="text-danger" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            @if ($required) required @endif
            @if ($resolvedError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
            {{ $attributes->class([
                'w-full rounded-radius border bg-surface h-9 px-3 text-sm text-on-surface-strong shadow-xs transition placeholder:text-on-surface/60 focus:outline-none focus:ring-3 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-white/5 dark:text-on-surface-dark-strong dark:placeholder:text-on-surface-dark/60',
                'border-outline hover:border-on-surface/30 focus:border-primary focus:ring-primary/15 dark:border-outline-dark dark:hover:border-white/20 dark:focus:border-primary-dark dark:focus:ring-primary-dark/20' => ! $resolvedError,
                'border-danger focus:ring-danger/15' => (bool) $resolvedError,
                'pr-9 [&::-webkit-calendar-picker-indicator]:opacity-0' => $hasList,
            ]) }}
        />

        @if ($hasList)
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="pointer-events-none absolute right-2.5 top-1/2 size-4 -translate-y-1/2 opacity-60" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
            </svg>
        @endif
    </div>

    @if ($hint && ! $resolvedError)
        <small class="text-xs text-on-surface/80 dark:text-on-surface-dark/80">{{ $hint }}</small>
    @endif

    @if ($resolvedError)
        <small id="{{ $id }}-error" class="text-xs text-danger">{{ $resolvedError }}</small>
    @endif
</div>
