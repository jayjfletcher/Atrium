@props(['href' => null])

<tr
    {{ $attributes->class([
        'bg-surface transition-colors hover:bg-surface-alt/70 dark:bg-surface-dark dark:hover:bg-white/[0.03]',
        'cursor-pointer' => $href !== null,
    ]) }}
    @if ($href) onclick="window.location='{{ $href }}'" @endif
>
    {{ $slot }}
</tr>
