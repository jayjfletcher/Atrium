<?php

declare(strict_types=1);

namespace Atrium\Atrium\Http\Controllers;

use Atrium\Atrium\Http\Requests\SaveDashboardLayoutRequest;
use Illuminate\Http\JsonResponse;

class DashboardLayoutController
{
    public function update(SaveDashboardLayoutRequest $request): JsonResponse
    {
        return $request->persist();
    }
}
