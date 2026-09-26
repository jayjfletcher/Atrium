@php($groups = \JayI\Atrium\Facades\Atrium::navigationGroups(request()))

<nav class="flex-1 overflow-x-hidden overflow-y-auto px-3 pb-2 rail:px-2" aria-label="{{ __('atrium::atrium.primary_navigation') }}"
     x-on:scroll="flyout = null">
    @foreach ($groups as $group => $items)
        <div class="mt-5 first:mt-1 rail:mt-2 rail:border-t rail:border-outline rail:pt-2 rail:first:mt-1 rail:first:border-t-0 rail:first:pt-0 dark:rail:border-outline-dark">
            @if ($group !== '')
                {{-- Group headings fold their items away. The rail has no
                     headings, so it always shows every group. --}}
                <button type="button"
                        class="group/heading flex w-full cursor-pointer items-center justify-between rounded-md px-2.5 pb-1.5 text-[11px] font-semibold uppercase tracking-wider text-on-surface/70 transition-colors hover:text-on-surface-strong rail:hidden dark:text-on-surface-dark/70 dark:hover:text-on-surface-dark-strong"
                        x-on:click="toggleGroup(@js($group))"
                        :aria-expanded="isGroupOpen(@js($group)).toString()">
                    {{ $group }}

                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-3.5 opacity-0 transition group-hover/heading:opacity-100 group-focus-visible/heading:opacity-100"
                         :class="isGroupOpen(@js($group)) || '-rotate-90 opacity-100!'" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </button>
            @endif

            <ul class="flex flex-col gap-0.5 rail:flex!" x-show="isGroupOpen(@js($group))">
                @foreach ($items as $item)
                    @include('atrium::partials.nav-item', ['item' => $item, 'depth' => 0])
                @endforeach
            </ul>
        </div>
    @endforeach
</nav>
