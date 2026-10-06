@props([
    'variant' => 'warning',
    // Inline styles instead of utility classes, for a banner rendered in the
    // host application's own pages, where Atrium's stylesheet is not loaded.
    'standalone' => false,
])

@php
    $colors = [
        'warning' => ['var(--color-warning, #b45309)', 'var(--color-on-warning, #ffffff)', 'bg-warning text-on-warning'],
        'danger' => ['var(--color-danger, #b91c1c)', 'var(--color-on-danger, #ffffff)', 'bg-danger text-on-danger'],
        'info' => ['var(--color-info, #1d4ed8)', 'var(--color-on-info, #ffffff)', 'bg-info text-on-info'],
    ][$variant] ?? ['var(--color-warning, #b45309)', 'var(--color-on-warning, #ffffff)', 'bg-warning text-on-warning'];
@endphp

{{-- A full-width notice across the top of a page, with optional actions. --}}
<div role="status"
    @if ($standalone)
        style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.75rem;padding:.5rem 1rem;background:{{ $colors[0] }};color:{{ $colors[1] }};font:500 .875rem/1.4 system-ui,sans-serif;"
        {{ $attributes }}
    @else
        {{ $attributes->class(['flex flex-wrap items-center justify-between gap-3 px-4 py-2 text-sm font-medium', $colors[2]]) }}
    @endif
>
    <span>{{ $slot }}</span>

    @isset($actions)
        <div @if ($standalone) style="display:flex;gap:.5rem;align-items:center" @else class="flex items-center gap-2" @endif>{{ $actions }}</div>
    @endisset
</div>
