<?php

declare(strict_types=1);

use Atrium\Atrium\Http\Requests\Request;
use Atrium\Atrium\Models\Dashboard;
use Atrium\Atrium\Plugins\PluginRegistry;
use Atrium\Atrium\Tests\Fixtures\AlphaPlugin;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Workbench\App\Models\User;

beforeEach(function (): void {
    ValidateCsrfToken::except(['*']);

    app()->detectEnvironment(fn (): string => 'local');
    app(PluginRegistry::class)->register(AlphaPlugin::class);
});

function persistUser(string $email): User
{
    return User::forceCreate(['name' => 'P', 'email' => $email, 'password' => bcrypt('x')]);
}

it('creates a dashboard through the request', function (): void {
    $user = persistUser('store@example.com');

    $this->actingAs($user)
        ->post(route('atrium.dashboards.store'), ['name' => 'Operations'])
        ->assertRedirect();

    expect(Dashboard::query()->where('name', 'Operations')->exists())->toBeTrue();
});

it('rejects a create with no name', function (): void {
    $this->actingAs(persistUser('invalid@example.com'))
        ->post(route('atrium.dashboards.store'), [])
        ->assertSessionHasErrors('name');
});

it('refuses to update a dashboard the user does not own', function (): void {
    $owner = persistUser('owner2@example.com');
    $intruder = persistUser('intruder2@example.com');

    $dashboard = Dashboard::query()->create([
        'name' => 'Theirs',
        'owner_type' => $owner->getMorphClass(),
        'owner_id' => $owner->getKey(),
    ]);

    $this->actingAs($intruder)
        ->put(route('atrium.dashboards.update', $dashboard), ['name' => 'Hijacked'])
        ->assertForbidden();

    expect($dashboard->fresh()->name)->toBe('Theirs');
});

it('updates a dashboard the user owns', function (): void {
    $user = persistUser('update2@example.com');

    $dashboard = Dashboard::query()->create([
        'name' => 'Before',
        'owner_type' => $user->getMorphClass(),
        'owner_id' => $user->getKey(),
    ]);

    $this->actingAs($user)
        ->put(route('atrium.dashboards.update', $dashboard), ['name' => 'After'])
        ->assertRedirect();

    expect($dashboard->fresh()->name)->toBe('After');
});

it('every atrium request declares how it persists', function (): void {
    $requests = glob(__DIR__.'/../../src/Http/Requests/*.php') ?: [];

    expect($requests)->not->toBeEmpty();

    foreach ($requests as $file) {
        $class = 'Atrium\\Atrium\\Http\\Requests\\'.basename($file, '.php');

        if ($class === Request::class) {
            continue;
        }

        $method = new ReflectionMethod($class, 'persist');

        expect($method->isPublic())->toBeTrue()
            ->and($method->getDeclaringClass()->getName())->toBe($class);
    }
});

it('keeps controllers free of business logic', function (): void {
    foreach (glob(__DIR__.'/../../src/Http/Controllers/*.php') ?: [] as $file) {
        $body = (string) file_get_contents($file);

        expect($body)->not->toContain('DB::')
            ->and($body)->not->toContain('->create(')
            ->and($body)->not->toContain('->delete(');
    }
});
