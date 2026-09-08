@props([
    'label' => null,
    'name',
    'checked' => false,
])

@php($id = $attributes->get('id', 'atrium-'.$name))

<div class="flex items-center gap-2">
    <input type="checkbox" id="{{ $id }}" name="{{ $name }}" @checked(old($name, $checked))
        {{ $attributes->class('relative h-6 w-11 cursor-pointer appearance-none rounded-full bg-surface-alt outline-none transition before:absolute before:top-0.5 before:left-0.5 before:size-5 before:rounded-full before:bg-surface before:shadow before:transition-all checked:bg-primary checked:before:translate-x-5 disabled:cursor-not-allowed disabled:opacity-75 dark:bg-surface-dark-alt dark:before:bg-surface-dark dark:checked:bg-primary-dark') }} />

    @if ($label)
        <label for="{{ $id }}" class="cursor-pointer text-sm text-on-surface dark:text-on-surface-dark">{{ $label }}</label>
    @endif
</div>
