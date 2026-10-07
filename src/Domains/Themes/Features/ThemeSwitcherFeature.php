<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\Themes\Features;

use Laravel\Pennant\Feature;

/**
 * The Pennant feature the theme switcher shows behind, named by default in
 * `atrium.themes.switcher_feature`.
 *
 * Atrium does not require Pennant: until something registers a feature
 * resolver (jayi/pennantplus does), every feature is on and this class is
 * never resolved. Under Pennant it is on globally until its global value is
 * set, and every other scope follows the global value until it is given its
 * own, so the switcher can be hidden for everyone or only for some people.
 */
class ThemeSwitcherFeature
{
    public function resolve(mixed $scope): bool
    {
        return $scope === null || Feature::for(null)->active(static::class);
    }
}
