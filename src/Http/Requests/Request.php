<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Http\Requests;

use RefactorCircus\Foundation\Http\Requests\Request as BaseRequest;

/**
 * Base class for Atrium's persistable requests.
 *
 * Each request owns its validation, its authorization, and the work itself.
 * Controllers collapse to `return $request->persist();`, so logic cannot drift
 * back into them: the abstract method forces every request to own the work.
 *
 * `persist()` should call an Action rather than writing to the database
 * directly, and return a response. `authorize()` checks the model the request
 * touches against its policy from `atrium.policies` through `allows()`, on top
 * of the dashboard gate the route middleware already enforced.
 */
abstract class Request extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
