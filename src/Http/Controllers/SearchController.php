<?php

declare(strict_types=1);

namespace Atrium\Atrium\Http\Controllers;

use Atrium\Atrium\Search\SearchRegistry;
use Atrium\Atrium\Search\SearchResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController
{
    public function __construct(protected SearchRegistry $search) {}

    public function __invoke(Request $request): JsonResponse
    {
        $query = $request->string('q')->toString();

        $results = array_map(
            fn (SearchResult $result): array => $result->toArray(),
            $this->search->search($request, $query),
        );

        return response()->json(['data' => $results]);
    }
}
