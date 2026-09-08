<?php

declare(strict_types=1);

namespace Atrium\Atrium\Tests\Feature;

use Atrium\Atrium\Tests\TestCase;
use Illuminate\Support\Facades\Gate;

class DashboardRoutesTest extends TestCase
{
    public function test_the_dashboard_registers_under_the_configured_path(): void
    {
        $this->assertStringContainsString('/atrium', route('atrium.dashboard'));
    }

    public function test_access_is_denied_outside_local_when_no_gate_is_defined(): void
    {
        $this->withEnvironment('production');

        $this->get('/atrium')->assertForbidden();
    }

    public function test_access_is_allowed_in_local_when_no_gate_is_defined(): void
    {
        $this->withEnvironment('local');

        $this->get('/atrium')->assertOk();
    }

    public function test_access_is_denied_when_the_gate_denies(): void
    {
        $this->withEnvironment('production');

        Gate::define('viewAtrium', fn ($user = null): bool => false);

        $this->get('/atrium')->assertForbidden();
    }

    public function test_access_is_allowed_when_the_gate_allows(): void
    {
        $this->withEnvironment('production');

        Gate::define('viewAtrium', fn ($user = null): bool => true);

        $this->get('/atrium')->assertOk();
    }

    public function test_the_gate_receives_the_authenticated_user(): void
    {
        $this->withEnvironment('production');

        $seen = null;

        Gate::define('viewAtrium', function ($user = null) use (&$seen): bool {
            $seen = $user;

            return true;
        });

        $this->get('/atrium')->assertOk();

        $this->assertNull($seen, 'A guest request reaches the gate with a null user.');
    }
}
