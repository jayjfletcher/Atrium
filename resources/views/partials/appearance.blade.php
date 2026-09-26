@php
    $atriumThemes = [
        'light' => [
            'label' => __('atrium::atrium.theme_light'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1.5m0 15V21m9-9h-1.5M4.5 12H3m15.364 6.364-1.06-1.06M6.697 6.697l-1.06-1.06m12.727 0-1.06 1.06M6.697 17.303l-1.06 1.06M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />',
        ],
        'dark' => [
            'label' => __('atrium::atrium.theme_dark'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />',
        ],
        'system' => [
            'label' => __('atrium::atrium.theme_system'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />',
        ],
    ];
@endphp

<div x-data="atriumAppearance()">
    <x-atrium::dropdown align="right">
        <x-slot:trigger>
            <button type="button" data-testid="appearance-toggle"
                    class="grid size-9 cursor-pointer place-items-center rounded-radius text-on-surface transition-colors hover:bg-on-surface-strong/5 hover:text-on-surface-strong dark:text-on-surface-dark dark:hover:bg-white/5 dark:hover:text-on-surface-dark-strong"
                    aria-label="{{ __('atrium::atrium.appearance') }}" title="{{ __('atrium::atrium.appearance') }}">
                {{-- Sun in light, moon in dark: whichever is actually showing. --}}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-[18px] dark:hidden" aria-hidden="true">{!! $atriumThemes['light']['icon'] !!}</svg>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="hidden size-[18px] dark:block" aria-hidden="true">{!! $atriumThemes['dark']['icon'] !!}</svg>
            </button>
        </x-slot:trigger>

        <div class="min-w-36" role="menu" aria-label="{{ __('atrium::atrium.appearance') }}">
            @foreach ($atriumThemes as $key => $option)
                <button type="button" role="menuitemradio" data-testid="theme-{{ $key }}"
                        class="flex w-full cursor-pointer items-center gap-2.5 rounded-md px-2 py-1.5 text-sm text-on-surface transition-colors hover:bg-on-surface-strong/5 hover:text-on-surface-strong dark:text-on-surface-dark dark:hover:bg-white/5 dark:hover:text-on-surface-dark-strong"
                        :class="theme === @js($key) && 'text-on-surface-strong! dark:text-on-surface-dark-strong! font-medium'"
                        :aria-checked="(theme === @js($key)).toString()"
                        x-on:click="choose(@js($key)); open = false">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0" aria-hidden="true">{!! $option['icon'] !!}</svg>
                    <span class="flex-1 text-left">{{ $option['label'] }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4 text-primary dark:text-primary-dark" x-show="theme === @js($key)" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                    </svg>
                </button>
            @endforeach
        </div>
    </x-atrium::dropdown>
</div>
