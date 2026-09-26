<?php

declare(strict_types=1);

namespace JayI\Atrium\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use JayI\Atrium\Events\Action\FeatureValueUpdatedActionEvent;
use JayI\Atrium\Events\Action\FeatureValueUpdatingActionEvent;
use JayI\Atrium\Pennant\FeatureFlagManager;
use JayI\Atrium\Pennant\StoredFeatureValue;
use Laravel\Pennant\Feature;

class UpdateFeatureValueAction extends Action
{
    public function __construct(protected FeatureFlagManager $features) {}

    /**
     * Store a feature's value for one serialized scope, through Pennant, so
     * its cache and events stay in step with the write.
     */
    protected function handle(string $feature, string $scope, mixed $value): StoredFeatureValue
    {
        FeatureValueUpdatingActionEvent::dispatch($feature, $scope, $value);

        $result = $this->perform($feature, $scope, $value);

        FeatureValueUpdatedActionEvent::dispatch($result);

        return $result;
    }

    private function perform(string $feature, string $scope, mixed $value): StoredFeatureValue
    {
        DB::transaction(function () use ($feature, $scope, $value): void {
            $this->features->store()
                ->for([$scope === Feature::serializeScope(null) ? null : $scope])
                ->activate($feature, $value);
        });

        return new StoredFeatureValue($feature, $scope, $value, Carbon::now());
    }
}
