<?php

declare(strict_types=1);

namespace Atrium\Atrium\Exceptions;

use InvalidArgumentException;

class DuplicateWidgetException extends InvalidArgumentException
{
    public static function forKey(string $key, string $existingPlugin, string $incomingPlugin): self
    {
        return new self(sprintf(
            'Widget key [%s] is already registered by plugin [%s]; plugin [%s] cannot reuse it.',
            $key,
            $existingPlugin,
            $incomingPlugin,
        ));
    }
}
