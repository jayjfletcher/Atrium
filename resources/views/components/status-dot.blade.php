{{--
    A status as a coloured dot, its label shown on hover and read by screen
    readers. Variants: success, warning, danger, info, primary, neutral.
    Keep `info` for pending - waiting on someone's decision - so it reads the
    same in every package.
--}}
@props([
    'label',
    'variant' => 'neutral',
])

<x-atrium::tooltip :text="$label">
    <span {{ $attributes->merge(['role' => 'img', 'aria-label' => $label])->class([
        'inline-block size-2.5 shrink-0 rounded-full',
        'bg-success' => $variant === 'success',
        'bg-warning' => $variant === 'warning',
        'bg-danger' => $variant === 'danger',
        'bg-info' => $variant === 'info',
        'bg-primary dark:bg-primary-dark' => $variant === 'primary',
        'bg-current opacity-45' => ! in_array($variant, ['success', 'warning', 'danger', 'info', 'primary'], true),
    ]) }}></span>
</x-atrium::tooltip>
