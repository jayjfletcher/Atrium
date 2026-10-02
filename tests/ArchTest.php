<?php

declare(strict_types=1);
use JayI\Atrium\Search\SearchRegistry;

arch()->preset()->php();

// SearchRegistry revives search results its own child processes wrote,
// restricted to SearchResult, as Laravel's process concurrency driver does.
arch()->preset()->security()->ignoring(SearchRegistry::class);

arch('it will not use dd(), ddd(), env(), or exit()')
    ->expect(['dd', 'ddd', 'env', 'exit'])
    ->each->not->toBeUsed();

arch('the package source declares strict types')
    ->expect('JayI\Atrium')
    ->toUseStrictTypes();
