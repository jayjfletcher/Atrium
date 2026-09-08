@props([
    'wrapper' => null,
    'label' => null,
    'name',
    'value' => 1,
    'checked' => false,
    'hint' => null,
    'error' => null,
])

@php
    $id = $attributes->get('id', 'atrium-'.$name);
    $resolvedError = $error ?? ($errors ?? null)?->first($name);
@endphp

<div @class(['flex flex-col gap-1 text-on-surface dark:text-on-surface-dark', $wrapper ?? 'w-full'])>
    <div class="flex items-center gap-2">
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="checkbox"
            value="{{ $value }}"
            @checked(old($name, $checked))
            {{ $attributes->class('size-4 shrink-0 cursor-pointer appearance-none rounded-sm border border-outline bg-surface-alt accent-primary checked:appearance-auto disabled:cursor-not-allowed disabled:opacity-75 dark:border-outline-dark dark:bg-surface-dark-alt/50 dark:accent-primary-dark') }}
        />

        @if ($label)
            <label for="{{ $id }}" class="cursor-pointer text-sm">{{ $label }}</label>
        @endif
    </div>

    @if ($hint && ! $resolvedError)
        <small class="pl-0.5 text-xs opacity-75">{{ $hint }}</small>
    @endif

    @if ($resolvedError)
        <small class="pl-0.5 text-xs text-danger">{{ $resolvedError }}</small>
    @endif
</div>
