<?php

declare(strict_types=1);

namespace Atrium\Atrium\Http\Controllers;

use Atrium\Atrium\Http\Requests\DeleteDashboardRequest;
use Atrium\Atrium\Http\Requests\StoreDashboardRequest;
use Atrium\Atrium\Http\Requests\UpdateDashboardRequest;
use Illuminate\Http\RedirectResponse;

class DashboardCrudController
{
    public function store(StoreDashboardRequest $request): RedirectResponse
    {
        return $request->persist();
    }

    public function update(UpdateDashboardRequest $request): RedirectResponse
    {
        return $request->persist();
    }

    public function destroy(DeleteDashboardRequest $request): RedirectResponse
    {
        return $request->persist();
    }
}
