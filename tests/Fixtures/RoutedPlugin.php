<?php

declare(strict_types=1);

namespace Atrium\Atrium\Tests\Fixtures;

use Atrium\Atrium\Plugins\Plugin;
use Illuminate\Support\Facades\Route;

class RoutedPlugin extends Plugin
{
    public function key(): string
    {
        return 'routed';
    }

    public function routes(): void
    {
        Route::get('routed', fn (): string => 'from plugin')->name('routed.index');
    }
}
