<?php

declare(strict_types=1);

namespace JayI\Atrium\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JayI\Atrium\Search\SearchRegistry;
use JayI\Atrium\Search\SearchResult;

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
