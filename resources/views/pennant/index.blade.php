@php
    use JayI\Atrium\Pennant\FeatureFlagManager;

    $scopeOptions = ['' => __('atrium::atrium.pennant_all_scopes'), FeatureFlagManager::GLOBAL => __('atrium::atrium.pennant_global')]
        + $scopeFilterOptions
        + [FeatureFlagManager::OTHER => __('atrium::atrium.pennant_other')];

    $formScopeOptions = [FeatureFlagManager::GLOBAL => __('atrium::atrium.pennant_global')]
        + collect($scopeModels)->mapWithKeys(fn ($scope): array => [$scope->class => $scope->label])->all()
        + [FeatureFlagManager::OTHER => __('atrium::atrium.pennant_other')];
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
            <form method="POST" action="{{ route('atrium.pennant.values.update') }}" class="grid items-start gap-3 sm:grid-cols-2 lg:grid-cols-5" data-testid="pennant-set-value">
                @csrf
                @method('PUT')

                <x-atrium::form.input name="feature" :label="__('atrium::atrium.pennant_feature')" list="atrium-pennant-features" required />
                <datalist id="atrium-pennant-features">
                    @foreach ($features as $feature)
                        <option value="{{ $feature }}"></option>
                    @endforeach
                </datalist>

                <div class="contents" x-data="atriumPennantScope(@js(route('atrium.pennant.scopes')), @js(old('scope_type', FeatureFlagManager::GLOBAL)))">
                    <x-atrium::form.select name="scope_type" id="atrium-pennant-scope-type" :label="__('atrium::atrium.pennant_scope')"
                                           :options="$formScopeOptions" :selected="FeatureFlagManager::GLOBAL"
                                           x-model="type" x-on:change="clear()" />

                    {{-- Global needs no ID; the other scope kinds each render their own field. --}}
                    <div class="w-full" x-show="type === @js(FeatureFlagManager::GLOBAL)"></div>

                    <template x-if="type === @js(FeatureFlagManager::OTHER)">
                        <x-atrium::form.input name="scope_id" id="atrium-pennant-scope-string" :label="__('atrium::atrium.pennant_scope_string')" />
                    </template>

                    <template x-if="type !== @js(FeatureFlagManager::GLOBAL) && type !== @js(FeatureFlagManager::OTHER)">
                        <div class="relative flex w-full flex-col gap-1.5 text-on-surface dark:text-on-surface-dark" x-on:click.outside="open = false">
                            <label for="atrium-pennant-scope-search" class="w-fit text-sm font-medium text-on-surface-strong dark:text-on-surface-dark-strong">{{ __('atrium::atrium.pennant_find_model') }}</label>

                            <input type="hidden" name="scope_id" x-bind:value="id">

                            <div x-show="id !== ''" class="flex h-9 items-center justify-between gap-2 rounded-radius border border-outline bg-surface px-3 text-sm text-on-surface-strong shadow-xs dark:border-outline-dark dark:bg-white/5 dark:text-on-surface-dark-strong">
                                <span x-text="title" data-testid="pennant-scope-picked"></span>
                                <button type="button" class="cursor-pointer rounded-md px-1.5 py-0.5 text-xs font-medium text-on-surface transition-colors hover:bg-on-surface-strong/5 hover:text-on-surface-strong dark:text-on-surface-dark dark:hover:bg-white/5 dark:hover:text-on-surface-dark-strong" x-on:click="clear()">{{ __('atrium::atrium.pennant_clear') }}</button>
                            </div>

                            <input x-show="id === ''" id="atrium-pennant-scope-search" type="search" autocomplete="off"
                                   placeholder="{{ __('atrium::atrium.pennant_find_model_placeholder') }}"
                                   class="h-9 w-full rounded-radius border border-outline bg-surface px-3 text-sm text-on-surface-strong shadow-xs transition placeholder:text-on-surface/60 hover:border-on-surface/30 focus:border-primary focus:outline-none focus:ring-3 focus:ring-primary/15 dark:border-outline-dark dark:bg-white/5 dark:text-on-surface-dark-strong dark:placeholder:text-on-surface-dark/60 dark:hover:border-white/20 dark:focus:border-primary-dark dark:focus:ring-primary-dark/20 [&::-webkit-search-cancel-button]:hidden"
                                   x-model="query" x-on:input.debounce.250ms="search()" x-on:focus="open = results.length > 0"
                                   data-testid="pennant-scope-search">

                            <ul x-show="open" x-cloak
                                class="absolute top-full z-20 mt-1.5 max-h-60 w-full overflow-y-auto rounded-radius border border-outline bg-surface p-1 text-sm shadow-xl dark:border-outline-dark dark:bg-surface-dark">
                                <template x-for="result in results" x-bind:key="result.id">
                                    <li>
                                        <button type="button" class="flex w-full cursor-pointer justify-between gap-3 rounded-md px-2 py-1.5 text-left text-on-surface-strong transition-colors hover:bg-on-surface-strong/5 dark:text-on-surface-dark-strong dark:hover:bg-white/5"
                                                x-on:click="pick(result)">
                                            <span x-text="result.title"></span>
                                            <span class="opacity-60" x-text="'#' + result.id"></span>
                                        </button>
                                    </li>
                                </template>
                                <li x-show="results.length === 0" class="px-3 py-1.5 opacity-75">{{ __('atrium::atrium.pennant_no_models') }}</li>
                            </ul>

                            @error('scope_id')
                                <small class="text-xs text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </template>
                </div>

                <x-atrium::form.input name="value" :label="__('atrium::atrium.pennant_value')" value="true" :hint="__('atrium::atrium.pennant_value_hint')" required />

                {{-- Offset by the label's height, so the button lines up with the fields. --}}
                <div class="sm:pt-6.5">
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
                                @if ($value->title)
                                    <span class="block text-xs opacity-75">{{ $value->title }}</span>
                                @endif
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

    @once
        @push('atrium-scripts')
            <script>
                window.atriumPennantScope = function (url, type) {
                    return {
                        type: type,
                        query: '',
                        results: [],
                        open: false,
                        id: '',
                        title: '',
                        clear() {
                            this.id = ''
                            this.title = ''
                            this.query = ''
                            this.results = []
                            this.open = false
                        },
                        pick(result) {
                            this.id = result.id
                            this.title = result.title + ' (#' + result.id + ')'
                            this.open = false
                        },
                        async search() {
                            if (this.query.trim() === '') { this.results = []; this.open = false; return }
                            const response = await fetch(
                                url + '?type=' + encodeURIComponent(this.type) + '&q=' + encodeURIComponent(this.query),
                                { headers: { 'Accept': 'application/json' } }
                            )
                            const payload = await response.json()
                            this.results = payload.data ?? []
                            this.open = true
                        },
                    }
                }
            </script>
        @endpush
    @endonce
</x-atrium::layout>
