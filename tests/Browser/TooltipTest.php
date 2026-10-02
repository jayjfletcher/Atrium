<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

beforeEach(function (): void {
    app()->detectEnvironment(fn (): string => 'local');

    Route::middleware('web')->get('/tooltip-fixture', fn () => view('clipped-tooltip'));
});

it('shows a tooltip outside a container that clips its overflow', function (): void {
    visit('/tooltip-fixture')
        ->hover('@tooltip-trigger')
        ->assertVisible('[role="tooltip"]')
        ->assertSee('A tooltip far wider than its box')
        // Teleported to <body>, and drawn wider than the box that clips its trigger.
        ->assertScript('document.querySelector(\'[role="tooltip"]\').parentElement === document.body', true)
        ->assertScript('document.querySelector(\'[role="tooltip"]\').getBoundingClientRect().width > document.querySelector(\'[data-testid="clipping-box"]\').getBoundingClientRect().width', true);
});

it('hides the tooltip when the pointer leaves', function (): void {
    visit('/tooltip-fixture')
        ->hover('@tooltip-trigger')
        ->assertVisible('[role="tooltip"]')
        ->hover('@elsewhere')
        ->assertMissing('[role="tooltip"]');
});
