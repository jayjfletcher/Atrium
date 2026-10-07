<x-atrium::layout :title="__('atrium::atrium.audit_log_for', ['package' => $package->label])">
    <x-atrium::page-header :title="__('atrium::atrium.audit_log_for', ['package' => $package->label])" />

    <div class="mt-5 flex flex-col gap-4">
        <x-atrium::card>
            <form method="GET" action="{{ route('atrium.history.show', $package->key) }}" class="flex flex-wrap items-start gap-3" data-testid="history-filters">
                <x-atrium::form.input name="action" :label="__('atrium::atrium.audit_action')" :value="$filters['action'] ?? null" :hint="__('atrium::atrium.audit_action_hint')" wrapper="w-56" />
                <x-atrium::form.input name="subject_type" :label="__('atrium::atrium.audit_subject_type')" :value="$filters['subject_type'] ?? null" wrapper="w-48" />
                <x-atrium::form.input name="subject_id" :label="__('atrium::atrium.audit_subject_id')" :value="$filters['subject_id'] ?? null" wrapper="w-40" />
                <x-atrium::form.actions>
                    <x-atrium::icon-button icon="funnel" :label="__('atrium::atrium.audit_filter')" variant="primary" type="submit" data-testid="filter-history" />
                    <x-atrium::icon-button icon="x-mark" :label="__('atrium::atrium.audit_clear')" variant="ghost" :href="route('atrium.history.show', $package->key)" />
                </x-atrium::form.actions>
            </form>
        </x-atrium::card>

        <x-atrium::audit.entries :entries="$page->entries" />

        @if (isset($filters['cursor']) || $page->nextCursor !== null)
            <nav class="flex items-center justify-end gap-2 text-sm" aria-label="{{ __('atrium::atrium.pagination') }}" data-testid="history-pages">
                @isset($filters['cursor'])
                    <x-atrium::button variant="secondary" size="sm" :href="route('atrium.history.show', [$package->key, ...array_filter(\Illuminate\Support\Arr::except($filters, ['cursor']))])">{{ __('atrium::atrium.audit_newest') }}</x-atrium::button>
                @endisset
                @if ($page->nextCursor !== null)
                    <x-atrium::button variant="secondary" size="sm" :href="route('atrium.history.show', [$package->key, ...array_filter([...$filters, 'cursor' => $page->nextCursor])])" data-testid="history-older">{{ __('atrium::atrium.audit_older') }}</x-atrium::button>
                @endif
            </nav>
        @endif
    </div>
</x-atrium::layout>
