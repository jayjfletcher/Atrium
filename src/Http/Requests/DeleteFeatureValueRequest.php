<?php

declare(strict_types=1);

namespace JayI\Atrium\Http\Requests;

use Illuminate\Http\RedirectResponse;
use JayI\Atrium\Actions\DeleteFeatureValueAction;
use JayI\Atrium\Http\Requests\Concerns\AuthorizesFeatureFlags;

class DeleteFeatureValueRequest extends Request
{
    use AuthorizesFeatureFlags;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'feature' => ['required', 'string', 'max:255'],
            'scope' => ['required', 'string', 'max:255'],
        ];
    }

    public function persist(): RedirectResponse
    {
        app(DeleteFeatureValueAction::class)->execute(
            $this->string('feature')->toString(),
            $this->string('scope')->toString(),
        );

        return redirect()->to($this->redirectToFeatureFlags())
            ->with('atrium.status', __('atrium::atrium.pennant_value_forgotten'));
    }
}
