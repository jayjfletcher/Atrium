<?php

declare(strict_types=1);

namespace Atrium\Atrium\Http\Controllers;

use Atrium\Atrium\Settings\SettingsRegistry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

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
