@props([
    'wrapper' => null,
    'label' => null,
    'name',
    'hint' => null,
    'error' => null,
    'multiple' => false,
])

@php
    $id = $attributes->get('id', 'atrium-'.$name);
    $resolvedError = $error ?? ($errors ?? null)?->first($name);
@endphp

<div @class(['flex flex-col gap-1.5 text-on-surface dark:text-on-surface-dark', $wrapper ?? 'w-full'])>
    @if ($label)
        <label for="{{ $id }}" class="w-fit text-sm font-medium text-on-surface-strong dark:text-on-surface-dark-strong">{{ $label }}</label>
    @endif

    <input type="file" id="{{ $id }}" name="{{ $name }}{{ $multiple ? '[]' : '' }}" @if ($multiple) multiple @endif
        {{ $attributes->class('w-full cursor-pointer rounded-radius border border-outline bg-surface text-sm shadow-xs file:mr-3 file:cursor-pointer file:border-0 file:border-r file:border-outline file:bg-surface-alt file:px-3 file:py-2 file:text-sm file:font-medium file:text-on-surface-strong focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary dark:border-outline-dark dark:bg-white/5 dark:file:border-outline-dark dark:file:bg-white/5 dark:file:text-on-surface-dark-strong dark:focus-visible:outline-primary-dark') }} />

    @if ($hint && ! $resolvedError)
        <small class="text-xs text-on-surface/80 dark:text-on-surface-dark/80">{{ $hint }}</small>
    @endif

    @if ($resolvedError)
        <small class="text-xs text-danger">{{ $resolvedError }}</small>
    @endif
</div>
