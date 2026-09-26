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
        ]) }}
    />

    @if ($hint && ! $resolvedError)
        <small class="text-xs text-on-surface/80 dark:text-on-surface-dark/80">{{ $hint }}</small>
    @endif

    @if ($resolvedError)
        <small id="{{ $id }}-error" class="text-xs text-danger">{{ $resolvedError }}</small>
    @endif
</div>
