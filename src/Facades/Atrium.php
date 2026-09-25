<?php

declare(strict_types=1);

namespace JayI\Atrium\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JayI\Atrium\Atrium
 */
class Atrium extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \JayI\Atrium\Atrium::class;
    }
}
