@props([
    'wrapper' => null,
    'label' => null,
    'name',
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
])

@php
    $id = $attributes->get('id', 'atrium-'.$name);
    $resolvedError = $error ?? ($errors ?? null)?->first($name);
    $current = old($name, $selected);
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
        <select
            id="{{ $id }}"
            name="{{ $name }}"
            @if ($required) required @endif
            @if ($resolvedError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
            {{ $attributes->class([
                'w-full cursor-pointer appearance-none rounded-radius border bg-surface h-9 pl-3 pr-9 text-sm text-on-surface-strong shadow-xs transition placeholder:text-on-surface/60 focus:outline-none focus:ring-3 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-white/5 dark:text-on-surface-dark-strong dark:placeholder:text-on-surface-dark/60',
                'border-outline hover:border-on-surface/30 focus:border-primary focus:ring-primary/15 dark:border-outline-dark dark:hover:border-white/20 dark:focus:border-primary-dark dark:focus:ring-primary-dark/20' => ! $resolvedError,
                'border-danger focus:ring-danger/15' => (bool) $resolvedError,
            ]) }}
        >
            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $optionLabel }}</option>
            @endforeach
        </select>

        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="pointer-events-none absolute right-2.5 top-1/2 size-4 -translate-y-1/2 opacity-60" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
        </svg>
    </div>

    @if ($hint && ! $resolvedError)
        <small class="text-xs text-on-surface/80 dark:text-on-surface-dark/80">{{ $hint }}</small>
    @endif

    @if ($resolvedError)
        <small id="{{ $id }}-error" class="text-xs text-danger">{{ $resolvedError }}</small>
    @endif
</div>
