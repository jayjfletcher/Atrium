{{-- Widgets are offered here. Nothing is placed on a dashboard until a
     user picks one, which is why the registry never auto-adds anything. --}}
<x-atrium::modal name="atrium-widget-picker" :title="__('atrium::atrium.add_widget')">
    @if (count($available) === 0)
        <p class="text-sm text-on-surface dark:text-on-surface-dark">{{ __('atrium::atrium.no_widgets_available') }}</p>
    @else
        <ul class="grid gap-2">
            @foreach ($available as $key => $definition)
                <li>
                    <button type="button"
                            class="w-full cursor-pointer rounded-radius border border-outline p-3 text-left transition hover:border-primary dark:border-outline-dark dark:hover:border-primary-dark"
                            data-testid="pick-{{ str_replace('.', '-', $key) }}"
                            x-on:click="add(@js($key), @js($definition->defaultWidth), @js($definition->defaultHeight))">
                        <span class="block text-sm font-medium text-on-surface-strong dark:text-on-surface-dark-strong">{{ $definition->label }}</span>

                        @if ($definition->description)
                            <span class="mt-0.5 block text-sm text-on-surface dark:text-on-surface-dark">{{ $definition->description }}</span>
                        @endif
                    </button>
                </li>
            @endforeach
        </ul>
    @endif
</x-atrium::modal>
