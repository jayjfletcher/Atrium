<?php

declare(strict_types=1);

namespace JayI\Atrium\Http\Requests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base class for Atrium's persistable requests.
 *
 * Each request owns its validation, its authorization, and the work itself.
 * Controllers collapse to `return $request->persist();`, so logic cannot drift
 * back into them: the abstract method forces every request to own the work.
 *
 * `persist()` should call an Action rather than writing to the database
 * directly, and return a response. `authorize()` checks the model the request
 * touches against its policy from `atrium.policies`, on top of the dashboard
 * gate the route middleware already enforced.
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

    /**
     * Check an ability against the model's policy from `atrium.policies`.
     *
     * Pass the model class for abilities such as `viewAny` and `create`, and
     * the instance for `view`, `update` and `delete`. A guest is refused.
     *
     * @param  Model|class-string<Model>  $subject
     * @param  array<int, mixed>  $arguments
     */
    protected function allows(string $ability, Model|string $subject, array $arguments = []): bool
    {
        return Gate::forUser($this->user())->allows($ability, [$subject, ...$arguments]);
    }

    /**
     * Check an ability against every model given; an empty list passes.
     *
     * @param  iterable<int, Model>  $models
     */
    protected function allowsEach(string $ability, iterable $models): bool
    {
        foreach ($models as $model) {
            if (! $this->allows($ability, $model)) {
                return false;
            }
        }

        return true;
    }
}
