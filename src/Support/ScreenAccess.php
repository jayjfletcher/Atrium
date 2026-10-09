<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use RefactorCircus\Keystone\Auth\Authorizer;
use RefactorCircus\Keystone\Packages\PackageRegistry;

/**
 * Whether the signed-in user may perform an ability in a package, asked the
 * way that package's JSON API and MCP tools ask it: the same ability on the
 * same model or model class, through its policies. Screens refuse with it and
 * hide controls with it, so a control shows exactly when its action is
 * allowed. With the package's `authorization` config key off, everything is.
 */
final class ScreenAccess
{
    /**
     * @param  Model|class-string<Model>  $subject
     * @param  array<int, mixed>  $arguments  Further policy arguments, after the subject.
     */
    public static function allows(string $package, string $ability, Model|string $subject, ?Request $request = null, array $arguments = []): bool
    {
        $user = ($request ?? request())->user();

        return Authorizer::for(app(PackageRegistry::class)->get($package))
            ->can($user instanceof Authenticatable ? $user : null, $ability, $subject, $arguments);
    }
}
