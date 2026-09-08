@props([
    'wrapper' => null,
    'label' => null,
    'name',
    'options' => [],
    'selected' => null,
    'error' => null,
])

@php
    $resolvedError = $error ?? ($errors ?? null)?->first($name);
    $current = old($name, $selected);
@endphp

<fieldset class="flex flex-col gap-2 text-on-surface dark:text-on-surface-dark">
    @if ($label)
        <legend class="pl-0.5 text-sm">{{ $label }}</legend>
    @endif

    @foreach ($options as $optionValue => $optionLabel)
        @php($id = 'atrium-'.$name.'-'.$optionValue)

        <div class="flex items-center gap-2">
            <input type="radio" id="{{ $id }}" name="{{ $name }}" value="{{ $optionValue }}"
                @checked((string) $current === (string) $optionValue)
                class="size-4 shrink-0 cursor-pointer appearance-none rounded-full border border-outline bg-surface-alt accent-primary checked:appearance-auto disabled:cursor-not-allowed disabled:opacity-75 dark:border-outline-dark dark:bg-surface-dark-alt/50 dark:accent-primary-dark" />

            <label for="{{ $id }}" class="cursor-pointer text-sm">{{ $optionLabel }}</label>
        </div>
    @endforeach

    @if ($resolvedError)
        <small class="pl-0.5 text-xs text-danger">{{ $resolvedError }}</small>
    @endif
</fieldset>
