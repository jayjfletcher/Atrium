<?php

declare(strict_types=1);

namespace JayI\Atrium\Tests\Fixtures;

use Illuminate\Support\Facades\Route;
use JayI\Atrium\Navigation\NavGroup;
use JayI\Atrium\Navigation\NavItem;
use JayI\Atrium\Plugins\Plugin;

class FeaturedPlugin extends Plugin
{
    public function key(): string
    {
        return 'featured';
    }

    public function features(): array
    {
        return ['beta'];
    }

    public function navigation(): array
    {
        return [
            NavItem::make('Featured Home')->url('/atrium/featured')->group('Featured'),
            NavItem::make('Featured Admin')->url('/atrium/featured/admin')->group('Featured Admin'),
        ];
    }

    public function navigationGroups(): array
    {
        return [
            NavGroup::make('Featured Admin')->can('manage-featured'),
        ];
    }

    public function routes(): void
    {
        Route::get('featured', fn (): string => 'featured page')->name('featured.index');
    }
}
