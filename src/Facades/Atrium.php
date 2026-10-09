<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RefactorCircus\Atrium\Atrium
 */
class Atrium extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \RefactorCircus\Atrium\Atrium::class;
    }
}
