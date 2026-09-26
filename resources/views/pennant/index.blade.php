@php
    use JayI\Atrium\Pennant\FeatureFlagManager;

    $scopeOptions = ['' => __('atrium::atrium.pennant_all_scopes'), FeatureFlagManager::GLOBAL => __('atrium::atrium.pennant_global')]
        + collect($scopeTypes)->mapWithKeys(fn (string $type): array => [$type => $type])->all()
        + [FeatureFlagManager::OTHER => __('atrium::atrium.pennant_other')];

    $formScopeOptions = array_diff_key($scopeOptions, ['' => true]);
@endphp

<x-atrium::layout :title="__('atrium::atrium.pennant_features')">
    <x-atrium::page-header :title="__('atrium::atrium.pennant_features')" :description="__('atrium::atrium.pennant_description')" />

    <div class="mt-5 flex flex-col gap-5">
        @if (session('atrium.status'))
            <x-atrium::alert variant="success" data-testid="pennant-status">{{ session('atrium.status') }}</x-atrium::alert>
        @endif

        @if ($errors->any())
            <x-atrium::alert variant="danger">{{ $errors->first() }}</x-atrium::alert>
        @endif

        @unless ($supported)
            <x-atrium::alert variant="warning" data-testid="pennant-unsupported">{{ __('atrium::atrium.pennant_unsupported') }}</x-atrium::alert>
        @endunless

        <x-atrium::card :title="__('atrium::atrium.pennant_set_value')">
            <form method="POST" action="{{ route('atrium.pennant.values.update') }}" class="grid items-end gap-3 sm:grid-cols-2 lg:grid-cols-5" data-testid="pennant-set-value">
                @csrf
                @method('PUT')

                <x-atrium::form.input name="feature" :label="__('atrium::atrium.pennant_feature')" list="atrium-pennant-features" required />
                <datalist id="atrium-pennant-features">
                    @foreach ($features as $feature)
                        <option value="{{ $feature }}"></option>
                    @endforeach
                </datalist>

                <x-atrium::form.select name="scope_type" id="atrium-pennant-scope-type" :label="__('atrium::atrium.pennant_scope')" :options="$formScopeOptions" :selected="FeatureFlagManager::GLOBAL" />
                <x-atrium::form.input name="scope_id" id="atrium-pennant-scope-id" :label="__('atrium::atrium.pennant_scope_id')" />
                <x-atrium::form.input name="value" :label="__('atrium::atrium.pennant_value')" value="true" :hint="__('atrium::atrium.pennant_value_hint')" required />

                <div class="pb-5">
                    <x-atrium::button type="submit">{{ __('atrium::atrium.pennant_save') }}</x-atrium::button>
                </div>
            </form>
        </x-atrium::card>

        @if ($supported)
            <form method="GET" action="{{ route('atrium.pennant.index') }}" class="grid items-end gap-3 sm:grid-cols-2 lg:grid-cols-4" data-testid="pennant-filters">
                <x-atrium::form.select name="feature" id="atrium-pennant-filter-feature" :label="__('atrium::atrium.pennant_feature')"
                                       :options="['' => __('atrium::atrium.pennant_all_features')] + array_combine($features, $features)"
                                       :selected="$filters['feature']" />
                <x-atrium::form.select name="scope" id="atrium-pennant-filter-scope" :label="__('atrium::atrium.pennant_scope')"
                                       :options="$scopeOptions" :selected="$filters['scope']" />
                <x-atrium::form.input name="scope_id" id="atrium-pennant-filter-scope-id" :label="__('atrium::atrium.pennant_scope_id')" :value="$filters['scope_id']" />

                <div class="flex gap-2">
                    <x-atrium::button type="submit" variant="outline">{{ __('atrium::atrium.pennant_filter') }}</x-atrium::button>
                    <x-atrium::button variant="ghost" :href="route('atrium.pennant.index')">{{ __('atrium::atrium.pennant_reset') }}</x-atrium::button>
                </div>
            </form>

            @if ($values->isEmpty())
                <x-atrium::empty-state :title="__('atrium::atrium.pennant_no_values')" />
            @else
                <x-atrium::table data-testid="pennant-values">
                    <x-slot:head>
                        <x-atrium::table.row>
                            <x-atrium::table.cell heading>{{ __('atrium::atrium.pennant_feature') }}</x-atrium::table.cell>
                            <x-atrium::table.cell heading>{{ __('atrium::atrium.pennant_scope') }}</x-atrium::table.cell>
                            <x-atrium::table.cell heading>{{ __('atrium::atrium.pennant_value') }}</x-atrium::table.cell>
                            <x-atrium::table.cell heading>{{ __('atrium::atrium.pennant_updated') }}</x-atrium::table.cell>
                            <x-atrium::table.cell heading />
                        </x-atrium::table.row>
                    </x-slot:head>

                    @foreach ($values as $value)
                        <x-atrium::table.row data-testid="pennant-value">
                            <x-atrium::table.cell class="font-medium">{{ $value->feature }}</x-atrium::table.cell>
                            <x-atrium::table.cell>
                                <span title="{{ $value->scope }}">{{ $value->scopeLabel() }}</span>
                            </x-atrium::table.cell>
                            <x-atrium::table.cell>
                                <x-atrium::badge :variant="$value->isActive() ? 'success' : 'neutral'">
                                    {{ $value->isActive() ? __('atrium::atrium.pennant_active') : __('atrium::atrium.pennant_inactive') }}
                                </x-atrium::badge>
                                @unless (is_bool($value->value))
                                    <code class="ml-1 text-xs">{{ \Illuminate\Support\Str::limit($value->displayValue(), 60) }}</code>
                                @endunless
                            </x-atrium::table.cell>
                            <x-atrium::table.cell>{{ $value->updatedAt?->diffForHumans() }}</x-atrium::table.cell>
                            <x-atrium::table.cell>
                                <div class="flex justify-end gap-2">
                                    <form method="POST" action="{{ route('atrium.pennant.values.update') }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="feature" value="{{ $value->feature }}">
                                        <input type="hidden" name="scope" value="{{ $value->scope }}">
                                        <input type="hidden" name="value" value="{{ $value->isActive() ? 'false' : 'true' }}">
                                        <x-atrium::button type="submit" size="sm" variant="outline">
                                            {{ $value->isActive() ? __('atrium::atrium.pennant_deactivate') : __('atrium::atrium.pennant_activate') }}
                                        </x-atrium::button>
                                    </form>

                                    <form method="POST" action="{{ route('atrium.pennant.values.destroy') }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="feature" value="{{ $value->feature }}">
                                        <input type="hidden" name="scope" value="{{ $value->scope }}">
                                        <x-atrium::button type="submit" size="sm" variant="ghost">{{ __('atrium::atrium.pennant_forget') }}</x-atrium::button>
                                    </form>
                                </div>
                            </x-atrium::table.cell>
                        </x-atrium::table.row>
                    @endforeach
                </x-atrium::table>

                <x-atrium::pagination :paginator="$values" />
            @endif

            @if ($filters['feature'] !== '')
                <form method="POST" action="{{ route('atrium.pennant.features.purge') }}"
                      onsubmit="return confirm(@js(__('atrium::atrium.pennant_purge_confirm')))">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="feature" value="{{ $filters['feature'] }}">
                    <x-atrium::button type="submit" variant="danger" size="sm" data-testid="pennant-purge">
                        {{ __('atrium::atrium.pennant_purge') }}: {{ $filters['feature'] }}
                    </x-atrium::button>
                </form>
            @endif
        @endif
    </div>
</x-atrium::layout>
