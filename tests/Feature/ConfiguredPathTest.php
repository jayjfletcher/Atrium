<?php

declare(strict_types=1);

namespace Atrium\Atrium\Tests\Feature;

use Atrium\Atrium\Tests\TestCase;
use Illuminate\Contracts\Config\Repository;

class ConfiguredPathTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app->make(Repository::class)->set('atrium.path', 'control-room');
    }

    public function test_the_dashboard_serves_from_the_configured_path(): void
    {
        $this->app->detectEnvironment(fn (): string => 'local');

        $this->get('/control-room')->assertOk();
    }

    public function test_the_default_path_is_no_longer_routed(): void
    {
        $this->app->detectEnvironment(fn (): string => 'local');

        $this->get('/atrium')->assertNotFound();
    }

    public function test_plugin_and_dashboard_routes_share_the_configured_prefix(): void
    {
        $this->assertStringContainsString('/control-room', route('atrium.dashboard'));
        $this->assertStringContainsString('/control-room/settings', route('atrium.settings'));
    }
}
