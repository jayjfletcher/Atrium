@props([
    'title' => null,
    'heading' => null,
])

{{--
    A standalone page outside the dashboard shell - an invitation, a
    confirmation - with Atrium's theme and stylesheet but no navigation:

        <x-atrium::guest :title="__('Join Acme')" :heading="__('Join Acme')">...</x-atrium::guest>
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    @include('atrium::partials.theme')

    <link rel="stylesheet" href="{{ asset('vendor/atrium/atrium.css') }}">

    @php($atriumStyles = app(\RefactorCircus\Atrium\Support\StyleRegistry::class))
    @foreach ($atriumStyles->stylesheets() as $href)
        <link rel="stylesheet" href="{{ $href }}">
    @endforeach
    @if ($atriumStyles->inline() !== [])
        <style>{!! implode("\n", $atriumStyles->inline()) !!}</style>
    @endif
</head>
<body class="bg-canvas text-on-surface antialiased dark:bg-canvas-dark dark:text-on-surface-dark">
    <main class="mx-auto flex min-h-dvh w-full max-w-md flex-col justify-center px-4 py-10" data-testid="guest-layout">
        <x-atrium::card {{ $attributes }}>
            @if ($heading)
                <h1 class="mb-3 text-lg font-semibold text-on-surface-strong dark:text-on-surface-dark-strong">{{ $heading }}</h1>
            @endif

            <div class="flex flex-col gap-4 text-sm">{{ $slot }}</div>
        </x-atrium::card>
    </main>

    <script src="{{ asset('vendor/atrium/atrium.js') }}"></script>
    @if (config('atrium.alpine', true))
        <script src="{{ asset('vendor/atrium/alpine.js') }}" defer></script>
    @endif
</body>
</html>
