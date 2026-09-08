<div class="relative w-full max-w-md" x-data="atriumSearch()">
    <label class="sr-only" for="atrium-search-input">{{ __('atrium::atrium.search') }}</label>

    <input id="atrium-search-input"
           type="search"
           class="w-full rounded-radius border border-outline bg-surface-alt px-3 py-1.5 text-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary dark:border-outline-dark dark:bg-surface-dark-alt/50 dark:focus-visible:outline-primary-dark"
           autocomplete="off"
           placeholder="{{ __('atrium::atrium.search_placeholder') }}"
           x-model="query"
           x-on:input.debounce.250ms="run()"
           x-on:focus="open = true"
           x-on:keydown.escape="close()">

    <div class="absolute inset-x-0 top-full z-30 mt-1.5 max-h-80 overflow-y-auto rounded-radius border border-outline bg-surface shadow-lg dark:border-outline-dark dark:bg-surface-dark"
         x-show="open && query.length > 0" x-cloak x-on:click.outside="close()">
        <template x-if="results.length === 0 && ! loading">
            <p class="px-3 py-3 text-sm text-on-surface dark:text-on-surface-dark">{{ __('atrium::atrium.no_results') }}</p>
        </template>

        <ul class="p-1">
            <template x-for="result in results" :key="result.url + result.title">
                <li>
                    <a :href="result.url" class="block rounded-radius px-2.5 py-2 transition hover:bg-surface-alt dark:hover:bg-surface-dark-alt">
                        <span class="block text-sm text-on-surface-strong dark:text-on-surface-dark-strong" x-text="result.title"></span>
                        <template x-if="result.subtitle">
                            <span class="block text-xs text-on-surface dark:text-on-surface-dark" x-text="result.subtitle"></span>
                        </template>
                    </a>
                </li>
            </template>
        </ul>
    </div>
</div>

@once
    @push('atrium-scripts')
        <script>
            window.atriumSearch = function () {
                return {
                    query: '',
                    results: [],
                    loading: false,
                    open: false,
                    close() { this.open = false },
                    async run() {
                        if (this.query.length === 0) { this.results = []; return }
                        this.loading = true
                        try {
                            const response = await fetch(
                                @js(route('atrium.search')) + '?q=' + encodeURIComponent(this.query),
                                { headers: { 'Accept': 'application/json' } }
                            )
                            const payload = await response.json()
                            this.results = payload.data ?? []
                            this.open = true
                        } finally {
                            this.loading = false
                        }
                    },
                }
            }
        </script>
    @endpush
@endonce
