<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Access\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use RefactorCircus\Atrium\Domains\Access\Services\Gatekeeper;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responds 404 unless every named feature is on, asking the same resolver
 * the navigation does. Aliased as `atrium.feature`.
 */
class EnsureFeaturesAreEnabled
{
    public function __construct(protected Gatekeeper $gatekeeper) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$features): Response
    {
        foreach ($features as $feature) {
            abort_unless($this->gatekeeper->enabled($feature, $request), 404);
        }

        return $next($request);
    }
}
