<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains;

use Illuminate\Support\ServiceProvider;
use JayI\Atrium\Domains\Access\AccessServiceProvider;
use JayI\Atrium\Domains\Dashboard\DashboardServiceProvider;
use JayI\Atrium\Domains\Navigation\NavigationServiceProvider;
use JayI\Atrium\Domains\Plugins\PluginsServiceProvider;
use JayI\Atrium\Domains\Search\SearchServiceProvider;
use JayI\Atrium\Domains\Settings\SettingsServiceProvider;
use JayI\Atrium\Domains\Themes\ThemesServiceProvider;
use JayI\Atrium\Domains\Widgets\WidgetsServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * The domain service providers.
     *
     * @var array<int, class-string<ServiceProvider>>
     */
    private array $providers = [
        AccessServiceProvider::class,
        DashboardServiceProvider::class,
        NavigationServiceProvider::class,
        PluginsServiceProvider::class,
        SearchServiceProvider::class,
        SettingsServiceProvider::class,
        ThemesServiceProvider::class,
        WidgetsServiceProvider::class,
    ];

    public function register(): void
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }
}
