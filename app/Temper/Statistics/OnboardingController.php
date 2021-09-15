<?php

declare(strict_types=1);

namespace App\Temper\Statistics;

use App\Temper\Statistics\Requests\OnboardingStatisticsListRequest;
use App\Temper\Statistics\Services\OnboardingDataCollector;

class OnboardingController
{
    private OnboardingDataCollector $onboardingDataCollector;

    public function __construct(OnboardingDataCollector $onboardingDataCollector)
    {
        $this->onboardingDataCollector = $onboardingDataCollector;
    }

    public function index(OnboardingStatisticsListRequest $request)
    {
        $data = $this->onboardingDataCollector->gatherData(['json', 'csv'], [
            'csv_skip_header' => false,
        ]);

        // If we have a csv without header, we can build our own template
        $header = ['user_id', 'created_at', 'onboarding_perentage', 'count_applications', 'count_accepted_applications'];
        $data = $this->onboardingDataCollector->gatherData(['json', 'csv'], [
            'csv_skip_header' => true,
            'template' => $header,
        ]);

        dd($data);

        // @todo:
//        Process the request
//        Load the correct data loaders
//        Standardize output
//        Send out the resource
    }
}
