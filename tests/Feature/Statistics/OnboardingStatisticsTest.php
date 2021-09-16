<?php

declare(strict_types=1);

use App\Temper\Statistics\Adapters\ImportFromCsv;
use App\Temper\Statistics\Adapters\ImportFromJson;
use App\Temper\Statistics\Collections\OnboardingCollection;
use App\Temper\Statistics\Models\OnboardingStatistic;

beforeEach(function () {
    $validationDataSet = new OnboardingCollection();
    for ($i = 1; $i < 10; ++$i) {
        $data = new OnboardingStatistic(
            [
                'user_id' => $i,
                'created_at' => '2016-07-22',
                'onboarding_perentage' => 40,
                'count_applications' => 0,
                'count_accepted_applications' => 0,
            ]
        );
        $validationDataSet->addStatistic($data);
    }

    $this->validationData = $validationDataSet;

    $tsvData = '
1	2016-07-22	40	0	0
2	2016-07-22	40	0	0
        ';

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

    $this->responseData =
        [
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
            [
                'user_id' => 1,
                'created_at' => '2016-07-22',
                'onboarding_perentage' => 40,
                'count_applications' => 0,
                'count_accepted_applications' => 0,
            ],
            [
                'user_id' => 2,
                'created_at' => '2016-07-22',
                'onboarding_perentage' => 40,
                'count_applications' => 0,
                'count_accepted_applications' => 0,
            ],
        ];

    app()->bind(ImportFromCsv::class, function () use ($tsvData) {
        $mockCsvImporter = Mockery::mock(ImportFromCsv::class)->makePartial();
        $mockCsvImporter->shouldReceive('fetchData')->once()
            ->andReturn($tsvData);

        return $mockCsvImporter;
    });

    app()->bind(ImportFromJson::class, function () use ($jsonData) {
        $mockJsonImporter = Mockery::mock(ImportFromJson::class)->makePartial();
        $mockJsonImporter->shouldReceive('fetchData')->once()
            ->andReturn(json_encode($jsonData));

        return $mockJsonImporter;
    });
});

// End of setup, begin testing

it(
    'can get data from onboarding endpoint',
    function () {
        $response = $this->getJson('/api/statistics/onboarding/list');

        $expectedResponse['data'] = $this->responseData;
        $response->assertStatus(200)->assertJson($expectedResponse);
    }
);

it(
    'can get sorted data from onboarding endpoint',
    function () {
        $expectedResponse['data'] = collect($this->responseData)->sortByDesc('user_id')->values()->toArray();

        $response = $this->getJson('/api/statistics/onboarding/list?sort[user_id]=desc');
        $response->assertStatus(200)->assertJson($expectedResponse);
    }
);

it(
    'can get filtered data from onboarding endpoint',
    function () {
        $expectedResponse['data'] = collect($this->responseData)->where('user_id', 2)->values()->toArray();

        $response = $this->getJson('/api/statistics/onboarding/list?filter[user_id]=2');
        $response->assertStatus(200)->assertJson($expectedResponse);
    }
);
