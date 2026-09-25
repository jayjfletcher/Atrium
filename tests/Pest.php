<?php

declare(strict_types=1);

use JayI\Atrium\Tests\BrowserTestCase;
use JayI\Atrium\Tests\TestCase;

uses(TestCase::class)->in(__DIR__.'/Feature');

// Browser tests need Atrium's published assets on disk so Alpine boots.
uses(BrowserTestCase::class)->in(__DIR__.'/Browser');
