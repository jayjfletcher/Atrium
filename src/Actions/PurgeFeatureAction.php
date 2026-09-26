<?php

declare(strict_types=1);

namespace JayI\Atrium\Actions;

use Illuminate\Support\Facades\DB;
use JayI\Atrium\Events\Action\FeaturePurgedActionEvent;
use JayI\Atrium\Events\Action\FeaturePurgingActionEvent;
use JayI\Atrium\Pennant\FeatureFlagManager;

class PurgeFeatureAction extends Action
{
    public function __construct(protected FeatureFlagManager $features) {}

    /**
     * Forget every stored value of a feature, for every scope.
     */
    protected function handle(string $feature): void
    {
        FeaturePurgingActionEvent::dispatch($feature);

        $this->perform($feature);

        FeaturePurgedActionEvent::dispatch($feature);
    }

    private function perform(string $feature): void
    {
        DB::transaction(function () use ($feature): void {
            $this->features->store()->purge($feature);
        });
    }
}
