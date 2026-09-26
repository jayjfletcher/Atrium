<?php

declare(strict_types=1);
use JayI\Atrium\Http\Middleware\Authorize;
use JayI\Atrium\Models\Dashboard;
use JayI\Atrium\Models\DashboardWidget;
use JayI\Atrium\Policies\DashboardPolicy;
use JayI\Atrium\Policies\DashboardWidgetPolicy;

return [

    /*
    |--------------------------------------------------------------------------
    | Dashboard Path
    |--------------------------------------------------------------------------
    |
    | The URI path the Atrium dashboard is served from. Plugin routes are
    | registered beneath this prefix so every dashboard URL is consistent.
    |
    */

    'path' => 'atrium',

    /*
    |--------------------------------------------------------------------------
    | Dashboard Domain
    |--------------------------------------------------------------------------
    |
    | Optionally serve the dashboard from a dedicated subdomain. Leave null
    | to serve it from the application's default domain.
    |
    */

    'domain' => null,

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware applied to every Atrium route, including plugin routes. The
    | authorization middleware checks the gate defined below.
    |
    */

    'middleware' => [
        'web',
        Authorize::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Authorization Gate
    |--------------------------------------------------------------------------
    |
    | The gate ability checked before the dashboard is shown. Define it in a
    | service provider with Gate::define(). When the gate is not defined,
    | access is granted in the local environment and denied everywhere else.
    |
    */

    'gate' => 'viewAtrium',

    /*
    |--------------------------------------------------------------------------
    | Policies
    |--------------------------------------------------------------------------
    |
    | The policy the Gate uses for each model. Every dashboard request is
    | checked against these, on top of the gate above. By default a
    | dashboard's owner may do anything, everyone may view a shared
    | dashboard, and widget placements follow their dashboard. Point a model
    | at your own class to replace its policy.
    |
    */

    'policies' => [
        Dashboard::class => DashboardPolicy::class,
        DashboardWidget::class => DashboardWidgetPolicy::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins
    |--------------------------------------------------------------------------
    |
    | Plugins are discovered automatically from installed packages that
    | declare them under "extra.atrium.plugins" in their composer.json.
    |
    | Add plugin classes here to register them manually, and list plugin keys
    | under "disabled" to hide discovered plugins you do not want.
    |
    */

    'discover' => true,

    'plugins' => [
        //
    ],

    'disabled' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Pennant
    |--------------------------------------------------------------------------
    |
    | When laravel/pennant is installed, Atrium registers a plugin for
    | managing stored feature flag values. Set "enabled" to false to leave it
    | out. "store" picks the Pennant store to manage, null for the default;
    | listing needs a store backed by the database driver. "gate" optionally
    | names an ability checked on top of the dashboard gate.
    |
    */

    'pennant' => [
        'enabled' => true,
        'store' => null,
        'gate' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Alpine.js
    |--------------------------------------------------------------------------
    |
    | Atrium's interactive components are built on Alpine. Leave this true to
    | let Atrium serve the copy it ships with. Set it to false when the host
    | application already bundles Alpine, so the page does not load it twice.
    |
    */

    'alpine' => true,

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | These values override the design tokens the shipped stylesheet was
    | compiled with, so the dashboard can be rethemed at runtime without
    | rebuilding any assets. Any key here becomes "--color-{key}".
    |
    | See resources/css/atrium.css for the full list of available tokens.
    |
    */

    'theme' => [
        // 'primary' => '#4f46e5',
        // 'on-primary' => '#ffffff',
    ],

];
