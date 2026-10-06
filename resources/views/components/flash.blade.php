@props([
    // Show only the first error of these keys; null shows the first error of any.
    'keys' => null,
])

{{--
    The flash status and validation error at the top of a screen:
    `session('status')` as a dismissible success alert, then the first error.
--}}
@php
    $bag = $errors ?? null;
    $status = session('status');
    $error = null;

    if ($bag !== null && $keys === null) {
        $error = $bag->any() ? $bag->first() : null;
    } elseif ($bag !== null) {
        foreach ((array) $keys as $key) {
            if ($bag->has($key)) {
                $error = $bag->first($key);
                break;
            }
        }
    }
@endphp

{{-- Nothing at all when there is nothing to say, so no gap is left behind. --}}
@if ($status || $error)
    <div {{ $attributes->class('flex flex-col gap-4') }}>
        @if ($status)
            <x-atrium::alert variant="success" dismissible data-testid="flash-status">{{ $status }}</x-atrium::alert>
        @endif
        @if ($error)
            <x-atrium::alert variant="danger" data-testid="flash-error">{{ $error }}</x-atrium::alert>
        @endif
    </div>
@endif
