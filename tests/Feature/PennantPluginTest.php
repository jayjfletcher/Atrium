<?php

declare(strict_types=1);

namespace JayI\Atrium\Tests\Feature;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use JayI\Atrium\Pennant\FeatureFlagManager;
use JayI\Atrium\Pennant\StoredFeatureValue;
use JayI\Atrium\Plugins\PennantPlugin;
use JayI\Atrium\Plugins\PluginRegistry;
use JayI\Atrium\Tests\Fixtures\Features\Billing\InvoicingFeature;
use JayI\Atrium\Tests\Fixtures\Features\Shipping\TrackingRates;
use JayI\Atrium\Tests\TestCase;
use Laravel\Pennant\Feature;
use Laravel\Pennant\PennantServiceProvider;
use Orchestra\Testbench\Attributes\DefineEnvironment;

class PennantPluginTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            PennantServiceProvider::class,
            ...parent::getPackageProviders($app),
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(dirname(__DIR__, 2).'/vendor/laravel/pennant/database/migrations');
    }

    protected function disablePennant($app): void
    {
        $app->make(Repository::class)->set('atrium.pennant.enabled', false);
    }

    protected function useArrayStore($app): void
    {
        $app->make(Repository::class)->set('pennant.default', 'array');
    }

    protected function gatePennant($app): void
    {
        $app->make(Repository::class)->set('atrium.pennant.gate', 'manageFeatures');
    }

    public function test_the_plugin_registers_when_pennant_is_installed(): void
    {
        $this->assertInstanceOf(PennantPlugin::class, app(PluginRegistry::class)->get('pennant'));
    }

    #[DefineEnvironment('disablePennant')]
    public function test_the_plugin_stays_out_when_disabled_in_config(): void
    {
        $this->assertFalse(app(PluginRegistry::class)->has('pennant'));
    }

    public function test_it_lists_global_and_model_values(): void
    {
        [$ada, $bob] = [$this->user('ada@example.com'), $this->user('bob@example.com')];

        Feature::for(null)->activate('beta');
        Feature::for($ada)->activate('beta');
        Feature::for($bob)->deactivate('reports');

        $this->actingAs($ada)->get(route('atrium.pennant.index'))
            ->assertOk()
            ->assertSee('Global')
            ->assertSee('User #'.$ada->getKey())
            ->assertSee('User #'.$bob->getKey())
            ->assertSee('reports');
    }

    public function test_it_filters_by_global_scope(): void
    {
        $ada = $this->user();

        Feature::for(null)->activate('global-only');
        Feature::for($ada)->activate('model-only');

        $this->assertSame(['global-only'], $this->listed(['scope' => FeatureFlagManager::GLOBAL]));
    }

    public function test_it_filters_by_model_type_and_key(): void
    {
        [$ada, $bob] = [$this->user('ada@example.com'), $this->user('bob@example.com')];

        Feature::for(null)->activate('global-only');
        Feature::for($ada)->activate('ada-only');
        Feature::for($bob)->activate('bob-only');

        $this->assertSame(['ada-only', 'bob-only'], $this->listed(['scope' => User::class]));

        $this->assertSame(['bob-only'], $this->listed(['scope' => User::class, 'scope_id' => (string) $bob->getKey()]));
    }

    public function test_it_offers_the_model_types_that_have_values(): void
    {
        Feature::for(null)->activate('beta');
        Feature::for($this->user())->activate('beta');
        Feature::for('team-7')->activate('beta');

        $this->assertSame([User::class], app(FeatureFlagManager::class)->scopeTypes());
    }

    public function test_it_toggles_a_stored_value_by_its_serialized_scope(): void
    {
        $ada = $this->user();

        Feature::for($ada)->activate('beta');

        $this->actingAs($ada)->put(route('atrium.pennant.values.update'), [
            'feature' => 'beta',
            'scope' => Feature::serializeScope($ada),
            'value' => 'false',
        ])->assertRedirect(route('atrium.pennant.index'));

        Feature::flushCache();

        $this->assertFalse(Feature::for($ada)->active('beta'));
    }

    public function test_it_sets_a_value_for_a_picked_model(): void
    {
        config(['atrium.pennant.scopes' => [User::class]]);

        $ada = $this->user();

        $this->actingAs($ada)->put(route('atrium.pennant.values.update'), [
            'feature' => 'plan',
            'scope_type' => User::class,
            'scope_id' => (string) $ada->getKey(),
            'value' => '{"tier":"gold"}',
        ])->assertSessionHasNoErrors();

        Feature::flushCache();

        $this->assertSame(['tier' => 'gold'], Feature::for($ada)->value('plan'));
    }

    public function test_it_sets_a_global_value(): void
    {
        $this->actingAs($this->user())->put(route('atrium.pennant.values.update'), [
            'feature' => 'beta',
            'scope_type' => FeatureFlagManager::GLOBAL,
            'value' => 'true',
        ])->assertSessionHasNoErrors();

        Feature::flushCache();

        $this->assertTrue(Feature::for(null)->active('beta'));
    }

    public function test_a_model_scope_needs_a_key(): void
    {
        config(['atrium.pennant.scopes' => [User::class]]);

        $this->actingAs($this->user())->put(route('atrium.pennant.values.update'), [
            'feature' => 'beta',
            'scope_type' => User::class,
            'value' => 'true',
        ])->assertSessionHasErrors('scope_id');
    }

    public function test_a_model_scope_must_exist(): void
    {
        config(['atrium.pennant.scopes' => [User::class]]);

        $this->actingAs($this->user())->put(route('atrium.pennant.values.update'), [
            'feature' => 'beta',
            'scope_type' => User::class,
            'scope_id' => '999',
            'value' => 'true',
        ])->assertSessionHasErrors('scope_id');

        $this->assertDatabaseMissing('features', ['name' => 'beta']);
    }

    public function test_a_model_scope_must_be_configured(): void
    {
        $ada = $this->user();

        $this->actingAs($ada)->put(route('atrium.pennant.values.update'), [
            'feature' => 'beta',
            'scope_type' => User::class,
            'scope_id' => (string) $ada->getKey(),
            'value' => 'true',
        ])->assertSessionHasErrors('scope_type');
    }

    public function test_it_finds_models_of_a_configured_scope(): void
    {
        config(['atrium.pennant.scopes' => [User::class => ['search' => ['name', 'email'], 'title' => 'email']]]);

        $ada = $this->user('ada@example.com');
        $bob = $this->user('bob@example.com');

        $this->actingAs($ada)->getJson(route('atrium.pennant.scopes', ['type' => User::class, 'q' => 'bob']))
            ->assertOk()
            ->assertExactJson(['data' => [['id' => (string) $bob->getKey(), 'title' => 'bob@example.com']]]);

        $this->actingAs($ada)->getJson(route('atrium.pennant.scopes', ['type' => User::class, 'q' => (string) $ada->getKey()]))
            ->assertJsonPath('data.0.id', (string) $ada->getKey());
    }

    public function test_it_refuses_to_search_an_unconfigured_scope(): void
    {
        $this->actingAs($this->user())
            ->getJson(route('atrium.pennant.scopes', ['type' => User::class, 'q' => 'ada']))
            ->assertNotFound();
    }

    public function test_it_names_model_scoped_values_after_their_model(): void
    {
        config(['atrium.pennant.scopes' => [User::class => ['label' => 'People', 'title' => 'email']]]);

        $ada = $this->user('ada@example.com');

        Feature::for($ada)->activate('beta');

        $this->assertSame('ada@example.com', app(FeatureFlagManager::class)->paginate()->items()[0]->title);

        $this->actingAs($ada)->get(route('atrium.pennant.index'))
            ->assertOk()
            ->assertSee('People');
    }

    public function test_it_offers_every_discoverable_feature(): void
    {
        config(['atrium.pennant.features' => [dirname(__DIR__).'/Fixtures/Features/*']]);

        Feature::for(null)->activate('stored-only');

        $features = app(FeatureFlagManager::class)->features();

        $this->assertContains(InvoicingFeature::class, $features);
        $this->assertContains('shipping-tracking', $features);
        $this->assertContains('stored-only', $features);
        $this->assertNotContains(TrackingRates::class, $features);
    }

    public function test_it_forgets_a_stored_value(): void
    {
        $ada = $this->user();

        Feature::for($ada)->activate('beta');
        Feature::for(null)->activate('beta');

        $this->actingAs($ada)->delete(route('atrium.pennant.values.destroy'), [
            'feature' => 'beta',
            'scope' => Feature::serializeScope($ada),
        ])->assertRedirect();

        $this->assertDatabaseMissing('features', ['name' => 'beta', 'scope' => Feature::serializeScope($ada)]);
        $this->assertDatabaseHas('features', ['name' => 'beta', 'scope' => Feature::serializeScope(null)]);
    }

    public function test_it_purges_a_feature_for_every_scope(): void
    {
        $ada = $this->user();

        Feature::for($ada)->activate('beta');
        Feature::for(null)->activate('beta');
        Feature::for(null)->activate('reports');

        $this->actingAs($ada)->delete(route('atrium.pennant.features.purge'), ['feature' => 'beta'])
            ->assertRedirect(route('atrium.pennant.index'));

        $this->assertDatabaseMissing('features', ['name' => 'beta']);
        $this->assertDatabaseHas('features', ['name' => 'reports']);
    }

    #[DefineEnvironment('gatePennant')]
    public function test_the_configured_gate_guards_every_route(): void
    {
        Gate::define('manageFeatures', fn (User $user): bool => false);

        $ada = $this->user();

        $this->actingAs($ada)->get(route('atrium.pennant.index'))->assertForbidden();

        $this->actingAs($ada)->put(route('atrium.pennant.values.update'), [
            'feature' => 'beta',
            'scope_type' => FeatureFlagManager::GLOBAL,
            'value' => 'true',
        ])->assertForbidden();

        $this->assertDatabaseMissing('features', ['name' => 'beta']);
    }

    #[DefineEnvironment('useArrayStore')]
    public function test_it_explains_that_listing_needs_the_database_driver(): void
    {
        $this->actingAs($this->user())->get(route('atrium.pennant.index'))
            ->assertOk()
            ->assertSee('database driver');
    }

    protected function setUp(): void
    {
        parent::setUp();

        Gate::define('viewAtrium', fn (User $user): bool => true);
    }

    /**
     * @param  array{feature?: string|null, scope?: string|null, scope_id?: string|null}  $filters
     * @return array<int, string>
     */
    private function listed(array $filters): array
    {
        $features = collect(app(FeatureFlagManager::class)->paginate($filters)->items())
            ->map(fn (StoredFeatureValue $value): string => $value->feature)
            ->sort()
            ->values()
            ->all();

        return $features;
    }

    private function user(string $email = 'ada@example.com'): User
    {
        return User::forceCreate(['name' => 'Ada', 'email' => $email, 'password' => 'x']);
    }
}
