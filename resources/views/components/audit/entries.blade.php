@props([
    // list<\RefactorCircus\Foundation\Audit\Data\AuditEntry>
    'entries' => [],
    // Hide the subject column, for a single record's history.
    'withoutSubject' => false,
])

@php
    // Keen's dashboard section shows an entry in full; link to it when it
    // is installed.
    $link = \Illuminate\Support\Facades\Route::has('atrium.keen.entries.show');
@endphp

@if ($entries === [] || (is_countable($entries) && count($entries) === 0))
    <x-atrium::empty-state :title="__('atrium::atrium.audit_empty')" data-testid="audit-empty" />
@else
    <x-atrium::table striped {{ $attributes->merge(['data-testid' => 'audit-entries']) }}>
        <x-slot:head>
            <x-atrium::table.row>
                <x-atrium::table.cell heading>{{ __('atrium::atrium.audit_when') }}</x-atrium::table.cell>
                <x-atrium::table.cell heading>{{ __('atrium::atrium.audit_action') }}</x-atrium::table.cell>
                @unless ($withoutSubject)
                    <x-atrium::table.cell heading>{{ __('atrium::atrium.audit_subject') }}</x-atrium::table.cell>
                @endunless
                <x-atrium::table.cell heading>{{ __('atrium::atrium.audit_actor') }}</x-atrium::table.cell>
                <x-atrium::table.cell heading>{{ __('atrium::atrium.audit_surface') }}</x-atrium::table.cell>
            </x-atrium::table.row>
        </x-slot:head>

        @foreach ($entries as $entry)
            <x-atrium::table.row>
                <x-atrium::table.cell>
                    @if ($link)
                        <a class="underline-offset-2 hover:underline" href="{{ route('atrium.keen.entries.show', $entry->id) }}" title="{{ $entry->createdAt->toIso8601String() }}">{{ $entry->createdAt->diffForHumans() }}</a>
                    @else
                        <span title="{{ $entry->createdAt->toIso8601String() }}">{{ $entry->createdAt->diffForHumans() }}</span>
                    @endif
                </x-atrium::table.cell>
                <x-atrium::table.cell>
                    <code>{{ $entry->action }}</code>
                    @if ($entry->source === 'app')
                        <x-atrium::badge variant="info">{{ __('atrium::atrium.audit_source_app') }}</x-atrium::badge>
                    @endif
                </x-atrium::table.cell>
                @unless ($withoutSubject)
                    <x-atrium::table.cell>{{ $entry->subjectLabel ?? __('atrium::atrium.audit_none') }}</x-atrium::table.cell>
                @endunless
                <x-atrium::table.cell>{{ $entry->actorLabel ?? __('atrium::atrium.audit_system') }}</x-atrium::table.cell>
                <x-atrium::table.cell><x-atrium::badge>{{ $entry->surface }}</x-atrium::badge></x-atrium::table.cell>
            </x-atrium::table.row>
        @endforeach
    </x-atrium::table>
@endif
