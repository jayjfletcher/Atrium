<?php

declare(strict_types=1);

namespace Atrium\Atrium\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base class for Atrium's persistable requests.
 *
 * Each request owns its validation, its authorization, and the work itself.
 * Controllers collapse to `return $request->persist();`, so logic cannot drift
 * back into them: the abstract method forces every request to own the work.
 *
 * `persist()` should call an Action rather than writing to the database
 * directly, and return a response.
 */
abstract class Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Carry out the request and return its response.
     */
    abstract public function persist(): Response;
}
