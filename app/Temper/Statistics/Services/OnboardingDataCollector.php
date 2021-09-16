<?php

declare(strict_types=1);

namespace App\Temper\Statistics\Services;

use App\Temper\Statistics\Adapters\ImportFromCsv;
use App\Temper\Statistics\Adapters\ImportFromJson;
use App\Temper\Statistics\Collections\OnboardingCollection;
use League\Flysystem\FileNotFoundException;

class OnboardingDataCollector
{
    private ImportFromCsv $importFromCsv;
    private ImportFromJson $importFromJson;

    public function __construct(ImportFromCsv $importFromCsv, ImportFromJson $importFromJson)
    {
        $this->importFromCsv = $importFromCsv;
        $this->importFromJson = $importFromJson;
    }

    // @todo: swap options and sources with a class when there is time

    /**
     * @throws FileNotFoundException
     */
    public function gatherData(array $sources, array $options = []): OnboardingCollection
    {
        $items = new OnboardingCollection();

        if (in_array('json', $sources, true)) {
            $items = $this->getDataFromJsonSource($items);
        }

        if (in_array('csv', $sources, true)) {
            $items = $this->getDataFromCsvSource($options, $items);
        }

        return $items;
    }

    /**
     * @throws FileNotFoundException
     */
    protected function getDataFromJsonSource(OnboardingCollection $items): OnboardingCollection
    {
        return $items->merge($this->importFromJson->loadData('test-data/export.json'));
    }

    /**
     * @throws FileNotFoundException
     */
    protected function getDataFromCsvSource(array $options, OnboardingCollection $items): OnboardingCollection
    {
        $data = $this->importFromCsv;

        // Set the template style to define the csv fields
        if (array_key_exists('template', $options)) {
            $data->setTemplate($options['template']);
        }

        if (array_key_exists('csv_skip_header', $options)) {
            $data->skipFirstRow(array_key_exists('csv_skip_header', $options) && $options['csv_skip_header']);
        }

        $data = $data->loadData('test-data/export.tsv');

        return $items->merge($data);
    }
}
