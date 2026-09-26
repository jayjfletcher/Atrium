<?php

declare(strict_types=1);

namespace JayI\Atrium\Events\Action;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JayI\Atrium\Contracts\ActionFinishedEvent;
use JayI\Atrium\Pennant\StoredFeatureValue;

/**
 * A feature flag value was stored for a scope.
 */
final class FeatureValueUpdatedActionEvent implements ActionFinishedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public StoredFeatureValue $value,
    ) {}
}
