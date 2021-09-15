<?php

declare(strict_types=1);

namespace App\Temper\Statistics\Adapters;

use App\Temper\Statistics\Collections\OnboardingCollection;
use App\Temper\Statistics\Interfaces\ImporterInterface;
use App\Temper\Statistics\Models\OnboardingStatistic;
use Illuminate\Support\Collection;
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

    /**
     * @return Collection
     */
    protected function fetchData(string $source): array
    {
        $data = Storage::get($source);

        $data = explode("\n", $data);

        $rowCount = 0;

        foreach ($data as $line) {
            ++$rowCount;
            if ($this->skipFirstRow && 1 === $rowCount) {
                continue;
            }
            if (1 === $rowCount) {
                if (! $this->template) {
                    $headerData = explode("\t", $line);
                    $this->setTemplate($headerData);
                    continue;
                }
            }

            if ('' !== $line) {
                $rawData[] = explode("\t", $line);
            }
        }

        return $rawData;
    }

    private function sanitizeData(array $data): OnboardingCollection
    {
        $onboardingStatisticsCollection = new OnboardingCollection();

        if ($this->template) {
            $data = collect($data);

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
