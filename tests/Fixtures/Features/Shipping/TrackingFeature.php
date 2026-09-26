<?php

declare(strict_types=1);

namespace JayI\Atrium\Tests\Fixtures\Features\Shipping;

class TrackingFeature
{
    public string $name = 'shipping-tracking';

    public function resolve(mixed $scope): bool
    {
        return true;
    }
}
