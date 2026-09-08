<?php

declare(strict_types=1);

use Atrium\Atrium\Tests\BrowserTestCase;
use Atrium\Atrium\Tests\TestCase;

uses(TestCase::class)->in(__DIR__.'/Feature', __DIR__.'/Unit');

// Browser tests need Atrium's published assets on disk so Alpine boots.
uses(BrowserTestCase::class)->in(__DIR__.'/Browser');
