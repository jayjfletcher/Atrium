<?php

declare(strict_types=1);

namespace Atrium\Atrium\Tests;

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
