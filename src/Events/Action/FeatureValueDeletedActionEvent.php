<?php

declare(strict_types=1);

namespace JayI\Atrium\Events\Action;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JayI\Atrium\Contracts\ActionFinishedEvent;

/**
 * A feature flag's stored value was forgotten for a scope.
 */
final class FeatureValueDeletedActionEvent implements ActionFinishedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public string $feature,
        /** The scope as Pennant serializes it. */
        public string $scope,
    ) {}
}
