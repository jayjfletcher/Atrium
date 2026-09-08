@props(['paginator' => null])

@php
    // A cursor paginator has no total and no page numbers by design: it never
    // runs a COUNT, which is the point of using one over a table that only
    // grows. Both kinds expose previous/next URLs, so only the summary and the
    // "is there a previous page" test differ.
    $countable = $paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    $previous = $paginator?->previousPageUrl();
    $next = $paginator?->nextPageUrl();

    $link = 'rounded-radius border border-outline px-3 py-1.5 transition hover:bg-surface-alt dark:border-outline-dark dark:hover:bg-surface-dark-alt';
    $disabled = 'cursor-not-allowed rounded-radius border border-outline px-3 py-1.5 opacity-50 dark:border-outline-dark';
@endphp

@if ($paginator && $paginator->hasPages())
    <nav {{ $attributes->class('flex items-center justify-between gap-2 text-sm') }} aria-label="{{ __('atrium::atrium.pagination') }}">
        <div class="text-on-surface dark:text-on-surface-dark">
            @if ($countable)
                {{ __('atrium::atrium.showing', [
                    'first' => $paginator->firstItem(),
                    'last' => $paginator->lastItem(),
                    'total' => $paginator->total(),
                ]) }}
            @else
                {{ __('atrium::atrium.showing_count', ['count' => $paginator->count()]) }}
            @endif
        </div>

        <div class="flex items-center gap-1">
            @if ($previous)
                <a href="{{ $previous }}" class="{{ $link }}">{{ __('atrium::atrium.previous') }}</a>
            @else
                <span class="{{ $disabled }}">{{ __('atrium::atrium.previous') }}</span>
            @endif

            @if ($next)
                <a href="{{ $next }}" class="{{ $link }}">{{ __('atrium::atrium.next') }}</a>
            @else
                <span class="{{ $disabled }}">{{ __('atrium::atrium.next') }}</span>
            @endif
        </div>
    </nav>
@endif
