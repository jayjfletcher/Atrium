@props([
    'wrapper' => null,
    'label' => null,
    'name',
    // list<string>: the suggestions offered as the user types.
    'options' => [],
    'value' => null,
    'placeholder' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
    'empty' => null,
])

{{--
    A text field that filters a list of suggestions as the user types. Free
    text is still submitted, so a value missing from the list stays usable.
--}}
@php
    $id = $attributes->get('id', 'atrium-'.$name);
    $resolvedError = $error ?? ($errors ?? null)?->first($name);
@endphp

<div @class(['relative flex flex-col gap-1.5 text-on-surface dark:text-on-surface-dark', $wrapper ?? 'w-full'])
     x-data="atriumCombobox(@js(array_values($options)), @js(old($name, $value)))" x-on:click.outside="open = false">
    @if ($label)
        <label for="{{ $id }}" class="w-fit text-sm font-medium text-on-surface-strong dark:text-on-surface-dark-strong">
            {{ $label }}
            @if ($required)
                <span class="text-danger" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <input id="{{ $id }}" name="{{ $name }}" type="text" autocomplete="off" role="combobox"
               aria-autocomplete="list" aria-controls="{{ $id }}-options" x-bind:aria-expanded="open"
               @if ($required) required @endif
               @if ($placeholder) placeholder="{{ $placeholder }}" @endif
               value="{{ old($name, $value) }}"
               x-model="query" x-on:focus="show()" x-on:click="show()" x-on:input="show()"
               x-on:keydown.arrow-down.prevent="move(1)" x-on:keydown.arrow-up.prevent="move(-1)"
               x-on:keydown.enter="choose($event)" x-on:keydown.escape="open = false" x-on:keydown.tab="open = false"
               @if ($resolvedError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
               {{ $attributes->except('id')->class([
                   'h-9 w-full rounded-radius border bg-surface pl-3 pr-9 text-sm text-on-surface-strong shadow-xs transition placeholder:text-on-surface/60 focus:outline-none focus:ring-3 dark:bg-white/5 dark:text-on-surface-dark-strong dark:placeholder:text-on-surface-dark/60',
                   'border-outline hover:border-on-surface/30 focus:border-primary focus:ring-primary/15 dark:border-outline-dark dark:hover:border-white/20 dark:focus:border-primary-dark dark:focus:ring-primary-dark/20' => ! $resolvedError,
                   'border-danger focus:ring-danger/15' => (bool) $resolvedError,
               ]) }}>

        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="pointer-events-none absolute right-2.5 top-1/2 size-4 -translate-y-1/2 opacity-60" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
        </svg>
    </div>

    <ul id="{{ $id }}-options" role="listbox" x-show="open" x-cloak
        class="absolute top-full z-20 mt-1.5 max-h-60 w-full overflow-y-auto rounded-radius border border-outline bg-surface p-1 text-sm shadow-xl dark:border-outline-dark dark:bg-surface-dark">
        <template x-for="(option, index) in matches" x-bind:key="option">
            <li role="option" x-bind:aria-selected="index === active">
                <button type="button" tabindex="-1"
                        class="flex w-full cursor-pointer break-all rounded-md px-2 py-1.5 text-left text-on-surface-strong transition-colors hover:bg-on-surface-strong/5 dark:text-on-surface-dark-strong dark:hover:bg-white/5"
                        x-bind:class="index === active && 'bg-on-surface-strong/5 dark:bg-white/5'"
                        x-on:mouseenter="active = index" x-on:click="pick(option)" x-text="option"></button>
            </li>
        </template>
        <li x-show="matches.length === 0" class="px-3 py-1.5 opacity-75">{{ $empty ?? __('atrium::atrium.no_results') }}</li>
    </ul>

    @if ($hint && ! $resolvedError)
        <small class="text-xs text-on-surface/80 dark:text-on-surface-dark/80">{{ $hint }}</small>
    @endif

    @if ($resolvedError)
        <small id="{{ $id }}-error" class="text-xs text-danger">{{ $resolvedError }}</small>
    @endif
</div>
