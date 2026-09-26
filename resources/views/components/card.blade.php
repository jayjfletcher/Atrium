@props([
    'title' => null,
    'subtitle' => null,
    'padded' => true,
])

@php($hasHeader = $title || $subtitle || isset($actions))

<div {{ $attributes->class('overflow-hidden rounded-radius border border-outline bg-surface text-on-surface shadow-xs dark:border-outline-dark dark:bg-surface-dark dark:text-on-surface-dark') }}>
    @if ($hasHeader)
        <div class="flex items-start justify-between gap-3 px-5 pt-4">
            <div>
                @if ($title)
                    <h3 class="text-sm font-semibold text-on-surface-strong dark:text-on-surface-dark-strong">{{ $title }}</h3>
                @endif

                @if ($subtitle)
                    <p class="mt-0.5 text-sm text-on-surface dark:text-on-surface-dark">{{ $subtitle }}</p>
                @endif
            </div>

            @isset($actions)
                <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div @class(['p-5' => $padded && ! $hasHeader, 'px-5 pb-5 pt-3' => $padded && $hasHeader, 'mt-3' => ! $padded && $hasHeader])>{{ $slot }}</div>

    @isset($footer)
        <div class="border-t border-outline bg-surface-alt/60 px-5 py-3 dark:border-outline-dark dark:bg-white/[0.02]">{{ $footer }}</div>
    @endisset
</div>
