<x-atrium::layout :title="$panel->label">
    <x-atrium::page-header :title="$panel->label" :description="$panel->description" />

    <div class="mt-5">
        @if ($panel->component)
            <x-dynamic-component :component="$panel->component"
                                 :attributes="new \Illuminate\View\ComponentAttributeBag($panel->resolveData())" />
        @elseif ($panel->view)
            @include($panel->view, $panel->resolveData())
        @endif
    </div>
</x-atrium::layout>
