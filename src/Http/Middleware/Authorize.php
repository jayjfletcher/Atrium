<?php

declare(strict_types=1);

namespace Atrium\Atrium\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class Authorize
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($this->passes($request), 403);

        return $next($request);
    }

    protected function passes(Request $request): bool
    {
        $ability = config('atrium.gate');

        if (! is_string($ability) || $ability === '') {
            return $this->fallback();
        }

        if (! Gate::has($ability)) {
            return $this->fallback();
        }

        return Gate::forUser($request->user())->allows($ability, [$request]);
    }

    /**
     * With no gate defined, allow local development and deny everywhere else.
     */
    protected function fallback(): bool
    {
        return app()->environment('local');
    }
}
