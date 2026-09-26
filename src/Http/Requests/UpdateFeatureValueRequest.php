<?php

declare(strict_types=1);

namespace JayI\Atrium\Http\Requests;

use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use JayI\Atrium\Actions\UpdateFeatureValueAction;
use JayI\Atrium\Http\Requests\Concerns\AuthorizesFeatureFlags;
use JayI\Atrium\Pennant\FeatureFlagManager;

/**
 * Stores a feature flag value for a scope.
 *
 * The scope arrives either already serialized, from a row of stored values,
 * or built in the form: the global scope, a model configured in
 * `atrium.pennant.scopes` and its key, or any other string scope.
 */
class UpdateFeatureValueRequest extends Request
{
    use AuthorizesFeatureFlags;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'feature' => ['required', 'string', 'max:255'],
            'scope' => ['required_without:scope_type', 'string', 'max:255'],
            'scope_type' => [
                'required_without:scope',
                'string',
                Rule::in([
                    FeatureFlagManager::GLOBAL,
                    FeatureFlagManager::OTHER,
                    ...array_keys($this->features()->scopeModels()),
                ]),
            ],
            'scope_id' => [
                'exclude_if:scope_type,'.FeatureFlagManager::GLOBAL,
                'required_with:scope_type',
                'string',
                'max:255',
            ],
            'value' => ['required', 'json'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty() || ! $this->filled('scope_type')) {
                    return;
                }

                if ($this->builtScope() === null) {
                    $validator->errors()->add('scope_id', __('atrium::atrium.pennant_scope_missing'));
                }
            },
        ];
    }

    public function persist(): RedirectResponse
    {
        /** @var array{feature: string, scope?: string, value: string} $data */
        $data = $this->validated();

        app(UpdateFeatureValueAction::class)->execute(
            $data['feature'],
            $data['scope'] ?? (string) $this->builtScope(),
            json_decode($data['value'], true, flags: JSON_THROW_ON_ERROR),
        );

        return redirect()->to($this->redirectToFeatureFlags())
            ->with('atrium.status', __('atrium::atrium.pennant_value_saved'));
    }

    /**
     * The scope picked in the form, serialized the way Pennant stores it.
     */
    protected function builtScope(): ?string
    {
        return $this->features()->resolveScope(
            $this->string('scope_type')->toString(),
            $this->string('scope_id')->toString(),
        );
    }

    protected function features(): FeatureFlagManager
    {
        return app(FeatureFlagManager::class);
    }
}
