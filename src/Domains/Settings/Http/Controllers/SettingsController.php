<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Settings\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use RefactorCircus\Atrium\Domains\Settings\Services\SettingsRegistry;

class SettingsController
{
    public function __construct(protected SettingsRegistry $settings) {}

    public function index(Request $request): View
    {
        /** @var view-string $view */
        $view = 'atrium::settings.index';

        return view($view, [
            'panels' => $this->settings->panels($request),
        ]);
    }

    public function show(Request $request, string $panel): View
    {
        $resolved = $this->settings->find($request, $panel);

        abort_if($resolved === null, 404);

        /** @var view-string $view */
        $view = 'atrium::settings.show';

        return view($view, [
            'panels' => $this->settings->panels($request),
            'panel' => $resolved,
        ]);
    }
}
