<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Dashboard\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use JayI\Atrium\Domains\Dashboard\Http\Requests\DeleteDashboardRequest;
use JayI\Atrium\Domains\Dashboard\Http\Requests\StoreDashboardRequest;
use JayI\Atrium\Domains\Dashboard\Http\Requests\UpdateDashboardRequest;

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
