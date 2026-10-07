<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use JayI\Atrium\Domains\History\Http\Controllers\HistoryController;

Route::get('history/{package}', HistoryController::class)->name('history.show');
