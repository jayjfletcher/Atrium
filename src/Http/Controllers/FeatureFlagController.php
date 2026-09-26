<?php

declare(strict_types=1);

namespace JayI\Atrium\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use JayI\Atrium\Http\Requests\DeleteFeatureValueRequest;
use JayI\Atrium\Http\Requests\PurgeFeatureRequest;
use JayI\Atrium\Http\Requests\UpdateFeatureValueRequest;
use JayI\Atrium\Pennant\FeatureFlagManager;
use JayI\Atrium\Plugins\PluginRegistry;

class FeatureFlagController
{
    public function __construct(protected FeatureFlagManager $features) {}

    public function index(Request $request, PluginRegistry $plugins): View
    {
        abort_unless($plugins->get('pennant')?->authorize($request) === true, 403);

        $filters = [
            'feature' => $request->string('feature')->toString(),
            'scope' => $request->string('scope')->toString(),
            'scope_id' => $request->string('scope_id')->toString(),
        ];

        $supported = $this->features->supportsListing();

        /** @var view-string $view */
        $view = 'atrium::pennant.index';

        return view($view, [
            'supported' => $supported,
            'filters' => $filters,
            'features' => $this->features->features(),
            'scopeTypes' => $this->features->scopeTypes(),
            'values' => $supported ? $this->features->paginate($filters)->withQueryString() : null,
        ]);
    }

    public function update(UpdateFeatureValueRequest $request): RedirectResponse
    {
        return $request->persist();
    }

    public function destroy(DeleteFeatureValueRequest $request): RedirectResponse
    {
        return $request->persist();
    }

    public function purge(PurgeFeatureRequest $request): RedirectResponse
    {
        return $request->persist();
    }
}
