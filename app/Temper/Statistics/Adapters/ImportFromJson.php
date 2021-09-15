<?php

declare(strict_types=1);

namespace App\Temper\Statistics\Adapters;

use App\Temper\Statistics\Collections\OnboardingCollection;
use App\Temper\Statistics\Interfaces\ImporterInterface;
use App\Temper\Statistics\Models\OnboardingStatistic;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;

class ImportFromJson implements ImporterInterface
{
    /**
     * @throws FileNotFoundException
     */
    public function loadData(string $source): OnboardingCollection
    {
        if (false === Storage::exists($source)) {
            throw new FileNotFoundException($source);
        }

        $rawData = $this->fetchData($source);

        return $this->sanitizeData($rawData);
    }

    /**
     * @return Collection
     */
    protected function fetchData(string $source): string
    {
        return Storage::get($source);
    }

    private function sanitizeData(string $data): OnboardingCollection
    {
        $data = json_decode($data, true);

        $onboardingStatisticsCollection = new OnboardingCollection();

        foreach ($data as $item) {
            $onboardingStatisticsCollection->addStatistic(new OnboardingStatistic($item));
        }

        return $onboardingStatisticsCollection;
    }
}
