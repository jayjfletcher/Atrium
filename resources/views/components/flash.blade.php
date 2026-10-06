@props([
    // Show only the first error of these keys; null shows the first error of any.
    'keys' => null,
])

{{--
    The flash status and validation error at the top of a screen:
    `session('status')` as a dismissible success alert, then the first error.
--}}
<div {{ $attributes->class('flex flex-col gap-4 empty:hidden') }}>@if (session('status'))<x-atrium::alert variant="success" dismissible data-testid="flash-status">{{ session('status') }}</x-atrium::alert>@endif
@php($bag = $errors ?? null)
@if ($bag !== null)
    @if ($keys === null && $bag->any())
        <x-atrium::alert variant="danger" data-testid="flash-error">{{ $bag->first() }}</x-atrium::alert>
    @elseif ($keys !== null)
        @foreach ((array) $keys as $key)
            @if ($bag->has($key))
                <x-atrium::alert variant="danger" data-testid="flash-error">{{ $bag->first($key) }}</x-atrium::alert>
                @break
            @endif
        @endforeach
    @endif
@endif</div>
