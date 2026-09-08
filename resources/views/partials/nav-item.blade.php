@php($badge = $item->resolveBadge())
@php($active = $item->isActive(request()))

<li>
    <a data-testid="nav-item" href="{{ $item->resolveUrl() ?? '#' }}"
       @class([
           'flex items-center gap-2 rounded-radius px-2.5 py-2 text-sm transition',
           'bg-primary text-on-primary dark:bg-primary-dark dark:text-on-primary-dark' => $active,
           'hover:bg-surface-alt dark:hover:bg-surface-dark-alt' => ! $active,
       ])
       @if ($active) aria-current="page" @endif>
        @if ($item->icon)
            <span class="shrink-0" aria-hidden="true">{!! $item->icon !!}</span>
        @endif

        <span class="flex-1 truncate">{{ $item->label }}</span>

        @if ($badge !== null)
            {{-- On an active item the label is already inverted, so the badge
                 tints the current colour rather than painting its own ground. --}}
            <span @class([
                'rounded-full px-1.5 py-0.5 text-xs',
                'bg-surface-alt text-on-surface dark:bg-surface-dark-alt dark:text-on-surface-dark' => ! $active,
                'bg-white/20' => $active,
            ])>{{ $badge }}</span>
        @endif
    </a>

    @if ($item->children !== [])
        <ul class="ml-4 flex flex-col gap-0.5">
            @foreach ($item->children as $child)
                @include('atrium::partials.nav-item', ['item' => $child])
            @endforeach
        </ul>
    @endif
</li>
