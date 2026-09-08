@props([
    'lines' => 1,
    'circle' => false,
])

@if ($circle)
    <div {{ $attributes->class('size-10 animate-pulse rounded-full bg-surface-alt dark:bg-surface-dark-alt') }} aria-hidden="true"></div>
@else
    <div {{ $attributes->class('flex w-full flex-col gap-2') }} aria-hidden="true">
        @for ($i = 0; $i < (int) $lines; $i++)
            <div @class([
                'h-3 animate-pulse rounded-radius bg-surface-alt dark:bg-surface-dark-alt',
                'w-full' => $i + 1 < (int) $lines,
                'w-2/3' => $i + 1 === (int) $lines,
            ])></div>
        @endfor
    </div>
@endif
