<?php

declare(strict_types=1);

namespace Atrium\Atrium\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Base class for every event Atrium dispatches.
 *
 * Implementing ShouldDispatchAfterCommit means a listener never fires for a
 * write that was rolled back.
 */
abstract class Event implements ShouldDispatchAfterCommit
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
}
