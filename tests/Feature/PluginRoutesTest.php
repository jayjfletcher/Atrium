<?php

declare(strict_types=1);

namespace Atrium\Atrium\Tests\Feature;

use Atrium\Atrium\Tests\Fixtures\RoutedPlugin;
use Atrium\Atrium\Tests\TestCase;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Route;

class PluginRoutesTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app->make(Repository::class)->set('atrium.plugins', [RoutedPlugin::class]);
    }

    public function test_plugin_routes_register_inside_the_atrium_group(): void
    {
        $this->assertTrue(Route::has('atrium.routed.index'));

        $this->withEnvironment('local');

        $this->get('/atrium/routed')->assertOk()->assertSee('from plugin');
    }

    public function test_plugin_routes_inherit_the_atrium_middleware_stack(): void
    {
        $this->withEnvironment('production');

        $this->get('/atrium/routed')->assertForbidden();
    }

    public function test_plugin_routes_use_the_configured_dashboard_prefix(): void
    {
        $this->assertStringContainsString('/atrium/routed', route('atrium.routed.index'));
    }
}
