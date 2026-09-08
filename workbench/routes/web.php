<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Convenience for `composer serve`: sign in as the seeded user so the
// dashboard's editing features are reachable without a login screen.
Route::get('/login-demo', function () {
    Auth::loginUsingId(1);

    return redirect('/atrium');
});
