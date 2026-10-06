@props(['standalone' => false])

{{-- An outlined button for a banner's actions, in the banner's own colour. --}}
<button
    {{ $attributes->merge(['type' => 'submit']) }}
    @if ($standalone)
        style="font:inherit;padding:.25rem .75rem;border:1px solid currentColor;border-radius:.375rem;background:transparent;color:inherit;cursor:pointer"
    @else
        class="cursor-pointer rounded-radius border border-current bg-transparent px-3 py-1 text-inherit transition-opacity hover:opacity-80"
    @endif
>{{ $slot }}</button>
