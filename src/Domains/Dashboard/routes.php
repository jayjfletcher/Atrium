<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use JayI\Atrium\Domains\Dashboard\Http\Controllers\DashboardController;
use JayI\Atrium\Domains\Dashboard\Http\Controllers\DashboardCrudController;
use JayI\Atrium\Domains\Dashboard\Http\Controllers\DashboardLayoutController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::post('dashboards', [DashboardCrudController::class, 'store'])->name('dashboards.store');
Route::put('dashboards/{dashboard}', [DashboardCrudController::class, 'update'])->name('dashboards.update');
Route::delete('dashboards/{dashboard}', [DashboardCrudController::class, 'destroy'])->name('dashboards.destroy');
Route::put('dashboards/{dashboard}/layout', [DashboardLayoutController::class, 'update'])->name('dashboards.layout');

Route::get('d/{slug}', [DashboardController::class, 'show'])->name('dashboard.show');
