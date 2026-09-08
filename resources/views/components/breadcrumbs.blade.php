@props(['items' => []])

<nav {{ $attributes }} aria-label="{{ __('atrium::atrium.breadcrumbs') }}">
    <ol class="flex flex-wrap items-center gap-1 text-sm text-on-surface dark:text-on-surface-dark">
        @foreach ($items as $label => $url)
            @php($last = $loop->last)

            <li class="flex items-center gap-1">
                @if ($url && ! $last)
                    <a href="{{ $url }}" class="transition hover:text-on-surface-strong dark:hover:text-on-surface-dark-strong">{{ $label }}</a>
                @else
                    <span class="font-medium text-on-surface-strong dark:text-on-surface-dark-strong" @if ($last) aria-current="page" @endif>{{ $label }}</span>
                @endif

                @unless ($last)
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4 opacity-50" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                @endunless
            </li>
        @endforeach
    </ol>
</nav>
