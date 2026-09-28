<?php

declare(strict_types=1);

namespace JayI\Atrium\Exceptions;

use InvalidArgumentException;
use JayI\Atrium\Contracts\Plugin;

class InvalidPluginException extends InvalidArgumentException
{
    public static function notAPlugin(string $class): self
    {
        return new self(sprintf(
            'Class [%s] must implement [%s] to be registered as an Atrium plugin.',
            $class,
            Plugin::class,
        ));
    }

    public static function missingClass(string $class): self
    {
        return new self(sprintf(
            'Atrium plugin class [%s] does not exist.',
            $class,
        ));
    }

    public static function duplicateKey(string $key, string $existing, string $incoming): self
    {
        return new self(sprintf(
            'Atrium plugin key [%s] is already registered by [%s]; [%s] cannot reuse it.',
            $key,
            $existing,
            $incoming,
        ));
    }
}
