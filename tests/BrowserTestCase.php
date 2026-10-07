<?php

declare(strict_types=1);

namespace JayI\Atrium\Tests;

/**
 * Browser tests run against a real HTTP server, so Atrium's stylesheet and
 * scripts must exist under the test app's public path. Without them Alpine
 * never boots and every interaction silently does nothing.
 */
abstract class BrowserTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->publishAtriumAssets();
    }

    /**
     * Search sources run in the request here. The process driver spawns a PHP
     * process per source, which is slow and environment-sensitive on CI
     * runners and makes typing-and-seeing tests flaky; SearchTest covers it.
     */
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('atrium.search.concurrency', 'sync');
    }

    protected function publishAtriumAssets(): void
    {
        $target = public_path('vendor/atrium');

        if (! is_dir($target)) {
            mkdir($target, 0777, true);
        }

        foreach (glob(__DIR__.'/../public/*.{css,js}', GLOB_BRACE) ?: [] as $asset) {
            copy($asset, $target.'/'.basename($asset));
        }
    }
}
