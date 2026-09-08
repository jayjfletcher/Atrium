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

<div @class(['flex flex-col gap-1 text-on-surface dark:text-on-surface-dark', $wrapper ?? 'w-full'])>
    @if ($label)
        <label for="{{ $id }}" class="w-fit pl-0.5 text-sm">
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
            'w-full rounded-radius border bg-surface-alt px-2 py-2 text-sm focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-75 dark:bg-surface-dark-alt/50',
            'border-outline focus-visible:outline-primary dark:border-outline-dark dark:focus-visible:outline-primary-dark' => ! $resolvedError,
            'border-danger focus-visible:outline-danger' => (bool) $resolvedError,
        ]) }}
    />

    @if ($hint && ! $resolvedError)
        <small class="pl-0.5 text-xs opacity-75">{{ $hint }}</small>
    @endif

    @if ($resolvedError)
        <small id="{{ $id }}-error" class="pl-0.5 text-xs text-danger">{{ $resolvedError }}</small>
    @endif
</div>
