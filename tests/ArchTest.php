<?php

declare(strict_types=1);
use Illuminate\Database\Eloquent\Model;
use RefactorCircus\Atrium\Domains\Search\Services\SearchRegistry;

arch()->preset()->php();

// SearchRegistry revives search results its own child processes wrote,
// restricted to SearchResult, as Laravel's process concurrency driver does.
arch()->preset()->security()->ignoring(SearchRegistry::class);

arch('it will not use dd(), ddd(), env(), or exit()')
    ->expect(['dd', 'ddd', 'env', 'exit'])
    ->each->not->toBeUsed();

arch('the package source declares strict types')
    ->expect('RefactorCircus\Atrium')
    ->toUseStrictTypes();

arch('domain models are named for their entity and end in Model')
    ->expect('RefactorCircus\Atrium\Domains\Dashboard\Models')
    ->classes()
    ->toExtend(Model::class)
    ->toHaveSuffix('Model');
