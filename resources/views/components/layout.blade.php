{{-- Thin wrapper so pages can write <x-atrium::layout> instead of @extends. --}}
@props(['title' => null])

@include('atrium::layouts.app', [
    'title' => $title,
    'slot' => $slot,
    'brand' => $brand ?? null,
    'topbar' => $topbar ?? null,
    'topbarEnd' => $topbarEnd ?? null,
    'breadcrumbs' => $breadcrumbs ?? null,
    'header' => $header ?? null,
    'footer' => $footer ?? null,
    'sidebarFooter' => $sidebarFooter ?? null,
])
