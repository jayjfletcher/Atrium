<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Domains\Dashboard\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use RefactorCircus\Keystone\Contracts\ActionStartingEvent;

/**
 * A dashboard is about to be created.
 */
final class DashboardCreatingActionEvent implements ActionStartingEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        /** @var array{name: string, is_shared?: bool} */
        public array $data,
        public ?Model $owner = null,
    ) {}
}
