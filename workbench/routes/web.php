<?php

use Illuminate\Support\Facades\Route;

// `composer serve` signs in the seeded admin through /_workbench and starts
// at the dashboard (see testbench.yaml), so the root only points there.
Route::redirect('/', '/atrium');
