<?php

declare(strict_types=1);

use App\Temper\Statistics\Adapters\ImportFromJson;
use App\Temper\Statistics\Collections\OnboardingCollection;
use App\Temper\Statistics\Services\OnboardingDataCollector;

uses(Tests\TestCase::class);

beforeEach(function () {
    $jsonData = [
        [
            'user_id' => 3274,
            'created_at' => '2016-07-26',
            'onboarding_perentage' => 100,
            'count_applications' => 36,
            'count_accepted_applications' => 12,
        ],
        [
            'user_id' => 3301,
            'created_at' => '2016-07-26',
            'onboarding_perentage' => 100,
            'count_applications' => 15,
            'count_accepted_applications' => 5,
        ],
    ];

    app()->bind(ImportFromJson::class, function () use ($jsonData) {
        $mockJsonImporter = Mockery::mock(ImportFromJson::class)->makePartial();
        $mockJsonImporter->shouldReceive('fetchData')->once()
            ->andReturn(json_encode($jsonData));

        return $mockJsonImporter;
    });
});

// End of setup, begin testing

it(
    'can gather data',
    function () {
        $onboardingDataCollector = app()->make(OnboardingDataCollector::class);

        $data = $onboardingDataCollector->gatherData(['json']);

        $this->assertTrue(true);
        $this->assertInstanceOf(OnboardingCollection::class, $data);
    }
);
