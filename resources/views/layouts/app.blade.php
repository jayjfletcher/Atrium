{{-- The Atrium shell. Every region is a slot, and this file is publishable,
     so applications can replace any part of the chrome without forking. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    @include('atrium::partials.theme')

    <link rel="stylesheet" href="{{ asset('vendor/atrium/atrium.css') }}">

    @stack('atrium-head')
</head>
<body class="min-h-screen bg-surface-alt text-on-surface antialiased dark:bg-surface-dark dark:text-on-surface-dark">
<div class="flex min-h-screen" x-data="{ sidebar: false }">
    <a class="sr-only focus:not-sr-only focus:absolute focus:left-2 focus:top-2 focus:z-50 focus:rounded-radius focus:bg-surface focus:px-3 focus:py-2 dark:focus:bg-surface-dark" href="#atrium-main">
        {{ __('atrium::atrium.skip_to_content') }}
    </a>

    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-40 w-64 shrink-0 -translate-x-full flex-col border-r border-outline bg-surface transition-transform lg:static lg:flex lg:translate-x-0 dark:border-outline-dark dark:bg-surface-dark"
        :class="sidebar ? 'flex translate-x-0' : ''"
    >
        <div class="border-b border-outline px-4 py-3 font-semibold text-on-surface-strong dark:border-outline-dark dark:text-on-surface-dark-strong">
            @if (! empty($brand))
                {{ $brand }}
            @else
                <a href="{{ route('atrium.dashboard') }}">{{ config('app.name') }}</a>
            @endif
        </div>

        @include('atrium::partials.navigation')

        @if (! empty($sidebarFooter))
            <div class="mt-auto border-t border-outline px-4 py-3 dark:border-outline-dark">{{ $sidebarFooter }}</div>
        @endif
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        {{-- Topbar --}}
        <header class="flex items-center gap-3 border-b border-outline bg-surface px-4 py-2.5 dark:border-outline-dark dark:bg-surface-dark">
            <button type="button" class="cursor-pointer rounded-radius p-1.5 transition hover:bg-surface-alt lg:hidden dark:hover:bg-surface-dark-alt"
                    x-on:click="sidebar = ! sidebar" aria-label="{{ __('atrium::atrium.toggle_navigation') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            @if (! empty($topbar))
                {{ $topbar }}
            @else
                @include('atrium::partials.search')
            @endif

            @if (! empty($topbarEnd))
                <div class="ml-auto flex items-center gap-2">{{ $topbarEnd }}</div>
            @endif
        </header>

        @if (! empty($breadcrumbs))
            <nav class="border-b border-outline px-4 py-2 text-sm dark:border-outline-dark" aria-label="{{ __('atrium::atrium.breadcrumbs') }}">
                {{ $breadcrumbs }}
            </nav>
        @endif

        <main id="atrium-main" class="flex-1 p-4 lg:p-6">
            @if (! empty($header))
                <div class="mb-5">{{ $header }}</div>
            @endif

            {{ $slot }}
        </main>

        @if (! empty($footer))
            <footer class="border-t border-outline px-4 py-3 text-sm dark:border-outline-dark">{{ $footer }}</footer>
        @endif
    </div>
</div>

{{-- Atrium's own behavior is registered before Alpine boots, so the
     component functions exist by the time Alpine initialises them. --}}
<script src="{{ asset('vendor/atrium/atrium.js') }}"></script>
@stack('atrium-scripts')

@if (config('atrium.alpine', true))
    <script src="{{ asset('vendor/atrium/alpine.js') }}" defer></script>
@endif
</body>
</html>
