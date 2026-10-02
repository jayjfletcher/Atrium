{{--
    An icon-only button or link. The label is its accessible name and shows as
    a tooltip on hover and focus. `icon` names any Heroicons outline icon or a
    registered one; the rest matches <x-atrium::button>.
--}}
@props([
    'icon',
    'label',
    'href' => null,
    'variant' => 'ghost',
    'size' => 'md',
    'type' => 'button',
    'tooltip' => 'bottom',
])

<x-atrium::tooltip :text="$label" :position="$tooltip">
    <x-atrium::button :variant="$variant" :size="$size" :type="$type" :href="$href"
        {{ $attributes->merge(['aria-label' => $label])->class(['px-0!', 'w-9' => $size === 'md', 'w-8' => $size === 'sm', 'w-10' => $size === 'lg']) }}>
        {!! \JayI\Atrium\Support\Icons::svg($icon) !!}
    </x-atrium::button>
</x-atrium::tooltip>
