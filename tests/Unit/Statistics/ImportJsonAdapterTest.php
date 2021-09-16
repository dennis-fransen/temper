<?php

declare(strict_types=1);

use App\Temper\Statistics\Adapters\ImportFromJson;
use App\Temper\Statistics\Collections\OnboardingCollection;
use App\Temper\Statistics\Models\OnboardingStatistic;
use Illuminate\Support\Facades\Storage;

uses(Tests\TestCase::class);

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
});

// End of setup, begin testing

it(
    'validates file existence',
    function () {
        $adapter = new ImportFromJson();

        $reflection = new ReflectionClass(ImportFromJson::class);
        $method = $reflection->getMethod('loadData');

        $method->invokeArgs($adapter, ['test-file-name.txt']);
    }
)->throws(League\Flysystem\FileNotFoundException::class);

it(
    'can load data',
    function () {
        $adapter = new ImportFromJson();

        $data = '
        [
  {
    "user_id": 1,
    "created_at": "2016-07-22",
    "onboarding_perentage": 40,
    "count_applications": 0,
    "count_accepted_applications": 0
  },
  {
    "user_id": 2,
    "created_at": "2016-07-22",
    "onboarding_perentage": 40,
    "count_applications": 0,
    "count_accepted_applications": 0
  }]';

        // Mock our storage get to return the required
        Storage::shouldReceive('get')->once()->andReturn($data);

        $reflection = new ReflectionClass(ImportFromJson::class);
        $method = $reflection->getMethod('fetchData');
        $method->setAccessible(true);

        $result = $method->invokeArgs($adapter, ['test-file-name.txt']);

        $this->assertEquals($data, $result);
    }
);

it(
    'can sanitize data',
    function () {
        $adapter = new ImportFromJson();
        $data = '
        [
  {
    "user_id": 1,
    "created_at": "2016-07-22",
    "onboarding_perentage": 40,
    "count_applications": 0,
    "count_accepted_applications": 0
  }
  ]';

        $reflection = new ReflectionClass(ImportFromJson::class);
        $method = $reflection->getMethod('sanitizeData');
        $method->setAccessible(true);

        $result = $method->invokeArgs($adapter, [$data]);

        $this->assertEquals($this->validationData->take(1), $result);
    }
);

it(
    'can do a full run',
    function () {
        $csvAdapter = new ImportFromJson();

        $data = '
        [
  {
    "user_id": 1,
    "created_at": "2016-07-22",
    "onboarding_perentage": 40,
    "count_applications": 0,
    "count_accepted_applications": 0
  },
  {
    "user_id": 2,
    "created_at": "2016-07-22",
    "onboarding_perentage": 40,
    "count_applications": 0,
    "count_accepted_applications": 0
  }]';

        // Mock our storage get to return the required testdata
        Storage::shouldReceive('get')->once()->andReturn($data);
        Storage::shouldReceive('exists')->once()->andReturn(true);

        $reflection = new ReflectionClass(ImportFromJson::class);
        $method = $reflection->getMethod('loadData');
        $method->setAccessible(true);

        $result = $method->invokeArgs($csvAdapter, ['test-file-name.txt']);

        $this->assertEquals($this->validationData->take(2), $result);
    }
);
