<?php

declare(strict_types=1);

namespace JayI\Atrium\Http\Controllers;

use Illuminate\Http\JsonResponse;
use JayI\Atrium\Http\Requests\SaveDashboardLayoutRequest;

class DashboardLayoutController
{
    public function update(SaveDashboardLayoutRequest $request): JsonResponse
    {
        return $request->persist();
    }
}
