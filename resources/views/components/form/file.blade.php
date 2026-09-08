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

<div @class(['flex flex-col gap-1 text-on-surface dark:text-on-surface-dark', $wrapper ?? 'w-full'])>
    @if ($label)
        <label for="{{ $id }}" class="w-fit pl-0.5 text-sm">{{ $label }}</label>
    @endif

    <input type="file" id="{{ $id }}" name="{{ $name }}{{ $multiple ? '[]' : '' }}" @if ($multiple) multiple @endif
        {{ $attributes->class('w-full cursor-pointer rounded-radius border border-outline bg-surface-alt text-sm file:mr-4 file:cursor-pointer file:border-none file:bg-surface-dark-alt/10 file:px-4 file:py-2 file:text-sm file:font-medium focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary dark:border-outline-dark dark:bg-surface-dark-alt/50 dark:file:bg-surface-alt/10 dark:focus-visible:outline-primary-dark') }} />

    @if ($hint && ! $resolvedError)
        <small class="pl-0.5 text-xs opacity-75">{{ $hint }}</small>
    @endif

    @if ($resolvedError)
        <small class="pl-0.5 text-xs text-danger">{{ $resolvedError }}</small>
    @endif
</div>
