<?php

declare(strict_types=1);

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use JayI\Atrium\Domains\Plugins\Support\Plugin;
use JayI\Atrium\Support\ScreenAccess;
use JayI\Atrium\Tests\Fixtures\Billing\RefundController;
use JayI\Foundation\Packages\Package;
use JayI\Foundation\Packages\PackageRegistry;

it('asks the package policies the way its api does', function (): void {
    app(PackageRegistry::class)->register(Package::make('billing', 'Billing')->authorization());
    Gate::define('refund', fn (User $user): bool => $user->getKey() === 1);

    $ada = new User;
    $ada->id = 1;
    $bob = new User;
    $bob->id = 2;

    request()->setUserResolver(fn (): User => $ada);
    expect(ScreenAccess::allows('billing', 'refund', User::class))->toBeTrue();

    request()->setUserResolver(fn (): User => $bob);
    expect(ScreenAccess::allows('billing', 'refund', User::class))->toBeFalse();

    config()->set('billing.authorization', false);
    expect(ScreenAccess::allows('billing', 'refund', User::class))->toBeTrue();
});

it('reads the loadable features from config', function (): void {
    config()->set('billing.atrium.features', ['billing-beta', 'Missing\\Feature', stdClass::class, 7]);

    $plugin = new class extends Plugin
    {
        public function features(): array
        {
            return $this->featuresFromConfig('billing.atrium.features');
        }
    };

    expect($plugin->features())->toBe(['billing-beta', stdClass::class]);
});

it('refuses a screen for the package its controller belongs to', function (): void {
    app(PackageRegistry::class)->register(Package::make('billing', 'JayI\Atrium\Tests\Fixtures\Billing')->authorization());
    Gate::define('refund', fn (): bool => false);
    Route::get('refund', RefundController::class);

    $user = new User;
    $user->id = 1;

    $this->actingAs($user)->get('/refund')->assertForbidden();

    config()->set('billing.authorization', false);

    $this->actingAs($user)->get('/refund')->assertOk()->assertSee('refunded');
});

it('passes further policy arguments after the subject', function (): void {
    app(PackageRegistry::class)->register(Package::make('billing', 'Billing')->authorization());
    Gate::define('refund', fn (User $user, string $class, int $amount): bool => $amount < 100);

    $user = new User;
    $user->id = 1;
    request()->setUserResolver(fn (): User => $user);

    expect(ScreenAccess::allows('billing', 'refund', User::class, arguments: [50]))->toBeTrue()
        ->and(ScreenAccess::allows('billing', 'refund', User::class, arguments: [500]))->toBeFalse();
});
