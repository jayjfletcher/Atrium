@props([
    'title' => null,
    'description' => null,
])

<section {{ $attributes->class('mt-6 first:mt-0') }}>
    @if ($title || $description)
        <div class="mb-3">
            @if ($title)
                <h2 class="text-base font-semibold text-on-surface-strong dark:text-on-surface-dark-strong">{{ $title }}</h2>
            @endif

            @if ($description)
                <p class="mt-1 text-sm text-on-surface dark:text-on-surface-dark">{{ $description }}</p>
            @endif
        </div>
    @endif

    <div>{{ $slot }}</div>
</section>
