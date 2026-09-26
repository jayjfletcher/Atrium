<?php

declare(strict_types=1);

namespace JayI\Atrium\Http\Requests;

use Illuminate\Http\RedirectResponse;
use JayI\Atrium\Actions\PurgeFeatureAction;
use JayI\Atrium\Http\Requests\Concerns\AuthorizesFeatureFlags;

class PurgeFeatureRequest extends Request
{
    use AuthorizesFeatureFlags;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'feature' => ['required', 'string', 'max:255'],
        ];
    }

    public function persist(): RedirectResponse
    {
        app(PurgeFeatureAction::class)->execute($this->string('feature')->toString());

        return redirect()->route('atrium.pennant.index')
            ->with('atrium.status', __('atrium::atrium.pennant_feature_purged'));
    }
}
