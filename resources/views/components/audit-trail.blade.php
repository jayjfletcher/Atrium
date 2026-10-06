@props([
    // The package key whose history to show, such as `keystone`.
    'source' => null,
    // One record's history; omit for the whole package's.
    'subject' => null,
    // Only this action, or a prefix ending in a dot: `feature.`.
    'action' => null,
    'limit' => 20,
    'title' => null,
])

{{--
    A package's own history, read from whichever audit log is installed
    through jayi/foundation's AuditTrail. Renders nothing until one is
    (jayi/keen), so packages can place it unconditionally:

        <x-atrium::audit-trail source="keystone" :subject="$product" />
--}}

@php
    $trail = app(\JayI\Foundation\Audit\Contracts\AuditTrail::class);
    $page = $trail->available()
        ? $trail->entries(\JayI\Foundation\Audit\Data\AuditFilter::make()->source($source)->subject($subject)->action($action)->limit((int) $limit))
        : null;
@endphp

@if ($page !== null)
    <x-atrium::card :title="$title ?? __('atrium::atrium.audit_history')" :padded="false" {{ $attributes->merge(['data-testid' => 'audit-trail']) }}>
        <x-atrium::audit.entries :entries="$page->entries" :without-subject="$subject !== null" />

        @if ($page->nextCursor !== null && \Illuminate\Support\Facades\Route::has('atrium.keen.entries.index'))
            <x-slot:footer>
                <a class="text-sm underline-offset-2 hover:underline" href="{{ route('atrium.keen.entries.index', array_filter(['source' => $source, 'action' => $action, 'subject_type' => $subject?->getMorphClass(), 'subject_id' => $subject?->getKey()])) }}">{{ __('atrium::atrium.audit_view_all') }}</a>
            </x-slot:footer>
        @endif
    </x-atrium::card>
@endif
