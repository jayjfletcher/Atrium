<?php

declare(strict_types=1);

namespace Atrium\Atrium\Search;

use Atrium\Atrium\Plugins\PluginRegistry;
use Illuminate\Http\Request;

class SearchRegistry
{
    /** @var array<int, SearchSource> */
    protected array $extra = [];

    public function __construct(protected PluginRegistry $plugins) {}

    public function add(SearchSource $source): static
    {
        $this->extra[] = $source;

        return $this;
    }

    /**
     * Sources the given request may query.
     *
     * @return array<int, SearchSource>
     */
    public function sources(Request $request): array
    {
        $sources = $this->extra;

        foreach ($this->plugins->authorized($request) as $plugin) {
            $source = $plugin->search();

            if ($source instanceof SearchSource) {
                $sources[] = $source;
            }
        }

        return array_values(array_filter(
            $sources,
            fn (SearchSource $source): bool => $source->isAuthorized($request),
        ));
    }

    /**
     * Aggregate results across every authorized source.
     *
     * @return array<int, SearchResult>
     */
    public function search(Request $request, string $query): array
    {
        $query = trim($query);

        if ($query === '') {
            return [];
        }

        $results = [];

        foreach ($this->sources($request) as $source) {
            foreach ($source->results($query) as $result) {
                $results[] = $result;
            }
        }

        return $results;
    }
}
