<x-atrium::layout :title="__('atrium::atrium.settings')">
    <x-atrium::page-header :title="__('atrium::atrium.settings')" />

    @if (count($panels) === 0)
        <x-atrium::empty-state :title="__('atrium::atrium.no_settings_panels')" />
    @else
        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($panels as $panel)
                <x-atrium::card :title="$panel->label" :subtitle="$panel->description">
                    <x-atrium::button variant="outline" :href="route('atrium.settings.show', $panel->key)">
                        {{ $panel->label }}
                    </x-atrium::button>
                </x-atrium::card>
            @endforeach
        </div>
    @endif
</x-atrium::layout>
