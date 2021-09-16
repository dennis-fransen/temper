<?php

declare(strict_types=1);

namespace App\Temper\Statistics\Adapters;

use App\Temper\Statistics\Collections\OnboardingCollection;
use App\Temper\Statistics\Interfaces\ImporterInterface;
use App\Temper\Statistics\Models\OnboardingStatistic;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;

class ImportFromCsv implements ImporterInterface
{
    private bool $skipFirstRow = false;
    private array|bool $template = false;

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
     * Set a template to map the csv fields to understandable keys.
     */
    public function setTemplate(array $template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Skip the first row.
     */
    public function skipFirstRow(bool $skip): self
    {
        $this->skipFirstRow = $skip;

        return $this;
    }

    public function fetchData(string $source): string
    {
        return Storage::get($source);
    }

    private function sanitizeData(string $data): OnboardingCollection
    {
        $data = explode("\n", $data);

        $rowCount = 0;

        $cleanData = [];

        foreach ($data as $line) {
            ++$rowCount;
            if ($this->skipFirstRow && 1 === $rowCount) {
                continue;
            }
            if (1 === $rowCount) {
                if (! $this->template) {
                    $fieldNames = explode("\t", $line);
                    $this->setTemplate($fieldNames);
                    continue;
                }
            }

            if ('' !== str_replace(' ', '', $line)) {
                $cleanData[] = explode("\t", $line);
            }
        }

        $onboardingStatisticsCollection = new OnboardingCollection();

        if ($this->template) {
            $data = collect($cleanData);

            $template = $this->template;

            $data = $data->map(function ($item) use ($template) {
                $dataSet = [];
                $templateFieldCount = count($template);
                for ($i = 0; $i < $templateFieldCount; ++$i) {
                    $dataSet[$template[$i]] = $item[$i];
                }

                return $dataSet;
            });
        }

        foreach ($data as $item) {
            $onboardingStatisticsCollection->addStatistic(new OnboardingStatistic($item));
        }

        return $onboardingStatisticsCollection;
    }
}
