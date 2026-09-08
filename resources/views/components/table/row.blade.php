@props(['href' => null])

<tr
    {{ $attributes->class([
        'bg-surface dark:bg-surface-dark',
        'cursor-pointer hover:bg-surface-alt dark:hover:bg-surface-dark-alt' => $href !== null,
    ]) }}
    @if ($href) onclick="window.location='{{ $href }}'" @endif
>
    {{ $slot }}
</tr>
