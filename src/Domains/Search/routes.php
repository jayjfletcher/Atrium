<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use JayI\Atrium\Domains\Search\Http\Controllers\SearchController;

Route::get('search', SearchController::class)->name('search');
