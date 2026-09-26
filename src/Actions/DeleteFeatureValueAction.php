<?php

declare(strict_types=1);

namespace JayI\Atrium\Actions;

use Illuminate\Support\Facades\DB;
use JayI\Atrium\Events\Action\FeatureValueDeletedActionEvent;
use JayI\Atrium\Events\Action\FeatureValueDeletingActionEvent;
use JayI\Atrium\Pennant\FeatureFlagManager;
use Laravel\Pennant\Feature;

class DeleteFeatureValueAction extends Action
{
    public function __construct(protected FeatureFlagManager $features) {}

    /**
     * Forget a feature's stored value for one serialized scope, so Pennant
     * resolves it afresh the next time it is checked.
     */
    protected function handle(string $feature, string $scope): void
    {
        FeatureValueDeletingActionEvent::dispatch($feature, $scope);

        $this->perform($feature, $scope);

        FeatureValueDeletedActionEvent::dispatch($feature, $scope);
    }

    private function perform(string $feature, string $scope): void
    {
        DB::transaction(function () use ($feature, $scope): void {
            $this->features->store()
                ->for([$scope === Feature::serializeScope(null) ? null : $scope])
                ->forget($feature);
        });
    }
}
