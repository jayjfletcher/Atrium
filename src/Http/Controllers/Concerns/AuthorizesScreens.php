<?php

declare(strict_types=1);

namespace JayI\Atrium\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use JayI\Atrium\Support\ScreenAccess;
use JayI\Foundation\Packages\PackageRegistry;

/**
 * For a package's Atrium screen controllers: refuse with the same policy check
 * the package's JSON API and MCP tools make, for the package the controller
 * belongs to.
 */
trait AuthorizesScreens
{
    /**
     * @param  Model|class-string<Model>  $subject
     * @param  array<int, mixed>  $arguments  Further policy arguments, after the subject.
     */
    protected function authorizeScreen(string $ability, Model|string $subject, array $arguments = []): void
    {
        abort_unless(ScreenAccess::allows(app(PackageRegistry::class)->forOrFail(static::class)->key, $ability, $subject, arguments: $arguments), 403);
    }
}
