<?php

declare(strict_types=1);

namespace Atrium\Atrium\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Atrium\Atrium\Atrium
 */
class Atrium extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Atrium\Atrium\Atrium::class;
    }
}
