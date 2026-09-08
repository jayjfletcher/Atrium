@php($definition = $placement->definition())

{{-- Rendered server-side so widget views keep full Blade and Livewire
     support. Alpine adds the editing chrome and controls visibility. --}}
<div class="relative col-span-12 lg:col-[span_var(--atrium-widget-width)]"
     data-testid="widget-{{ str_replace('.', '-', $placement->widget_key) }}"
     data-widget-id="{{ $placement->id }}"
     data-widget-key="{{ $placement->widget_key }}"
     x-show="has(@js($placement->id))"
     :style="styleFor(@js($placement->id))"
     @if ($editable ?? false)
         draggable="true"
         x-on:dragstart="startDrag(@js($placement->id), $event)"
         x-on:dragover.prevent="dragOver(@js($placement->id))"
         x-on:drop.prevent="drop()"
         x-on:dragend="endDrag()"
     @endif>

    @if ($editable ?? false)
        <div class="absolute -top-3 right-1 z-10 flex items-center gap-0.5 rounded-radius border border-outline bg-surface p-0.5 shadow dark:border-outline-dark dark:bg-surface-dark"
             x-show="editing" x-cloak>
            <span class="cursor-grab px-1 text-on-surface/60 dark:text-on-surface-dark/60" title="{{ __('atrium::atrium.drag_to_reorder') }}">⠿</span>

            <button type="button" class="min-w-6 cursor-pointer rounded px-1 py-0.5 text-sm transition hover:bg-surface-alt dark:hover:bg-surface-dark-alt"
                    data-testid="narrower-{{ str_replace('.', '-', $placement->widget_key) }}"
                    x-on:click="widen(@js($placement->id), -1)"
                    aria-label="{{ __('atrium::atrium.narrower') }}">&minus;</button>

            <button type="button" class="min-w-6 cursor-pointer rounded px-1 py-0.5 text-sm transition hover:bg-surface-alt dark:hover:bg-surface-dark-alt"
                    data-testid="wider-{{ str_replace('.', '-', $placement->widget_key) }}"
                    x-on:click="widen(@js($placement->id), 1)"
                    aria-label="{{ __('atrium::atrium.wider') }}">+</button>

            <button type="button" class="min-w-6 cursor-pointer rounded px-1 py-0.5 text-sm transition hover:bg-danger hover:text-on-danger"
                    data-testid="remove-{{ str_replace('.', '-', $placement->widget_key) }}"
                    x-on:click="remove(@js($placement->id))"
                    aria-label="{{ __('atrium::atrium.remove') }}">&times;</button>
        </div>
    @endif

    @if ($definition?->component)
        <x-dynamic-component :component="$definition->component"
                             :attributes="new \Illuminate\View\ComponentAttributeBag($definition->resolveData($placement->settings ?? []))" />
    @elseif ($definition?->view)
        @include($definition->view, $definition->resolveData($placement->settings ?? []))
    @endif
</div>
