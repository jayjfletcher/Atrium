@props([
    // Server-rendered state; with Alpine, bind `:aria-pressed` instead.
    'active' => false,
    'href' => null,
])

{{--
    A pill that toggles a filter. Its pressed look follows `aria-pressed`, so
    Alpine can drive it: <x-atrium::chip x-on:click="tag = 'x'" x-bind:aria-pressed="tag === 'x'">.
--}}
@php
    $classes = 'inline-flex cursor-pointer items-center rounded-full px-2.5 py-0.5 text-xs font-medium transition-colors bg-surface-alt text-on-surface hover:bg-on-surface-strong/10 aria-pressed:bg-primary aria-pressed:text-on-primary dark:bg-surface-dark-alt dark:text-on-surface-dark dark:aria-pressed:bg-primary-dark dark:aria-pressed:text-on-primary-dark';
@endphp

@if ($href)
    <a href="{{ $href }}" aria-pressed="{{ $active ? 'true' : 'false' }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="button" aria-pressed="{{ $active ? 'true' : 'false' }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
