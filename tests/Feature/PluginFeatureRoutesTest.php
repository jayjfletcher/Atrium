<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Tests\Feature;

use Illuminate\Contracts\Config\Repository;
use RefactorCircus\Atrium\Facades\Atrium;
use RefactorCircus\Atrium\Tests\Fixtures\FeaturedPlugin;
use RefactorCircus\Atrium\Tests\TestCase;

class PluginFeatureRoutesTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app->make(Repository::class)->set('atrium.plugins', [FeaturedPlugin::class]);
    }

    public function test_plugin_routes_answer_while_its_features_are_on(): void
    {
        $this->withEnvironment('local');

        Atrium::resolveFeaturesUsing(fn (string $feature): bool => true);

        $this->get('/atrium/featured')->assertOk()->assertSee('featured page');
    }

    public function test_plugin_routes_are_not_found_while_a_feature_is_off(): void
    {
        $this->withEnvironment('local');

        Atrium::resolveFeaturesUsing(fn (string $feature): bool => $feature !== 'beta');

        $this->get('/atrium/featured')->assertNotFound();
    }
}
