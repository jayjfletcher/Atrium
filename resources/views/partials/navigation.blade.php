@php($groups = \Atrium\Atrium\Facades\Atrium::navigationGroups(request()))

<nav class="flex-1 overflow-y-auto p-2" aria-label="{{ __('atrium::atrium.primary_navigation') }}">
    @foreach ($groups as $group => $items)
        <div class="mt-3 first:mt-0">
            @if ($group !== '')
                <p class="px-2 pb-1 text-xs font-medium uppercase tracking-wide text-on-surface/60 dark:text-on-surface-dark/60">{{ $group }}</p>
            @endif

            <ul class="flex flex-col gap-0.5">
                @foreach ($items as $item)
                    @include('atrium::partials.nav-item', ['item' => $item])
                @endforeach
            </ul>
        </div>
    @endforeach
</nav>
