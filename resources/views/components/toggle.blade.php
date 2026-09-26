@props([
    'label' => null,
    'name',
    'checked' => false,
])

@php($id = $attributes->get('id', 'atrium-'.$name))

<div class="flex items-center gap-2">
    <input type="checkbox" id="{{ $id }}" name="{{ $name }}" @checked(old($name, $checked))
        {{ $attributes->class('relative h-5 w-9 shrink-0 cursor-pointer appearance-none rounded-full bg-on-surface-strong/15 outline-none transition-colors before:absolute before:top-0.5 before:left-0.5 before:size-4 before:rounded-full before:bg-white before:shadow-sm before:transition-transform checked:bg-primary checked:before:translate-x-4 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary disabled:cursor-not-allowed disabled:opacity-50 dark:bg-white/15 dark:checked:bg-primary-dark') }} />

    @if ($label)
        <label for="{{ $id }}" class="cursor-pointer text-sm text-on-surface dark:text-on-surface-dark">{{ $label }}</label>
    @endif
</div>
