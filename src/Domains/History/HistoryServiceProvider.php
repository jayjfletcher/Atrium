<?php

declare(strict_types=1);

namespace JayI\Atrium\Domains\History;

use JayI\Atrium\Support\ServiceProvider;

/**
 * Each package's own audit log in the dashboard, read from whichever audit
 * log is installed through jayi/foundation's AuditTrail.
 */
class HistoryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadDashboardRoutesFrom(__DIR__.'/routes.php');
    }
}
