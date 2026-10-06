@props([
    // Just the control - no wrapper, label, hint or error text - for a
    // table cell or an inline row that labels it some other way.
    'bare' => false,
    'wrapper' => null,
    'label' => null,
    'name',
    'value' => 1,
    'checked' => false,
    // The tick colour: `primary`, or `danger` for a destructive choice.
    'accent' => 'primary',
    'hint' => null,
    'error' => null,
])

@php
    $id = $attributes->get('id', 'atrium-'.$name);
    $resolvedError = $error ?? ($errors ?? null)?->first($name);
@endphp

<div @class(['flex flex-col gap-1 text-on-surface dark:text-on-surface-dark' => ! $bare, $wrapper ?? 'w-full' => ! $bare, 'contents' => $bare])>
    <div class="flex items-center gap-2">
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="checkbox"
            value="{{ $value }}"
            @checked(old($name, $checked))
            {{ $attributes->except('id')->class(['size-4 shrink-0 cursor-pointer appearance-none rounded-sm border border-outline bg-surface shadow-xs checked:appearance-auto focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary disabled:cursor-not-allowed disabled:opacity-60 dark:border-white/20 dark:bg-white/5', $accent === 'danger' ? 'accent-danger' : 'accent-primary dark:accent-primary-dark']) }}
        />

        @if ($label)
            <label for="{{ $id }}" class="cursor-pointer text-sm">{{ $label }}</label>
        @endif
    </div>

    @if ($hint && ! $resolvedError && ! $bare)
        <small class="text-xs text-on-surface/80 dark:text-on-surface-dark/80">{{ $hint }}</small>
    @endif

    @if ($resolvedError && ! $bare)
        <small class="text-xs text-danger">{{ $resolvedError }}</small>
    @endif
</div>
