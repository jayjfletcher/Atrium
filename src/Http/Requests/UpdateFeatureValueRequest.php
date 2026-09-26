<?php

declare(strict_types=1);

namespace JayI\Atrium\Http\Requests;

use Illuminate\Http\RedirectResponse;
use JayI\Atrium\Actions\UpdateFeatureValueAction;
use JayI\Atrium\Http\Requests\Concerns\AuthorizesFeatureFlags;
use JayI\Atrium\Pennant\FeatureFlagManager;

/**
 * Stores a feature flag value for a scope.
 *
 * The scope arrives either already serialized, from a row of stored values,
 * or as a `scope_type` and `scope_id` picked in the form, which is
 * serialized the way Pennant stores it.
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
            'scope_type' => ['required_without:scope', 'string', 'max:255'],
            'scope_id' => [
                'exclude_if:scope_type,'.FeatureFlagManager::GLOBAL,
                'required_with:scope_type',
                'string',
                'max:255',
            ],
            'value' => ['required', 'json'],
        ];
    }

    public function persist(): RedirectResponse
    {
        /** @var array{feature: string, scope?: string, scope_type?: string, scope_id?: string|null, value: string} $data */
        $data = $this->validated();

        $scope = $data['scope'] ?? app(FeatureFlagManager::class)->serializeScope(
            (string) ($data['scope_type'] ?? FeatureFlagManager::GLOBAL),
            $data['scope_id'] ?? null,
        );

        app(UpdateFeatureValueAction::class)->execute(
            $data['feature'],
            $scope,
            json_decode($data['value'], true, flags: JSON_THROW_ON_ERROR),
        );

        return redirect()->to($this->redirectToFeatureFlags())
            ->with('atrium.status', __('atrium::atrium.pennant_value_saved'));
    }
}
