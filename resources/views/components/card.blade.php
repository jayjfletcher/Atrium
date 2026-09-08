@props([
    'title' => null,
    'subtitle' => null,
    'padded' => true,
])

<div {{ $attributes->class('overflow-hidden rounded-radius border border-outline bg-surface text-on-surface dark:border-outline-dark dark:bg-surface-dark dark:text-on-surface-dark') }}>
    @if ($title || $subtitle || isset($actions))
        <div class="flex items-start justify-between gap-3 border-b border-outline px-5 py-3 dark:border-outline-dark">
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

    <div @class(['p-5' => $padded])>{{ $slot }}</div>

    @isset($footer)
        <div class="border-t border-outline px-5 py-3 dark:border-outline-dark">{{ $footer }}</div>
    @endisset
</div>
