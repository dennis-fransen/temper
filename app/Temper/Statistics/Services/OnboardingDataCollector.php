<?php

declare(strict_types=1);

namespace App\Temper\Statistics\Services;

use App\Temper\Statistics\Adapters\ImportFromCsv;
use App\Temper\Statistics\Adapters\ImportFromJson;
use App\Temper\Statistics\Collections\OnboardingCollection;

class OnboardingDataCollector
{
    // @todo: swap options with a class when there is time
    public function gatherData(array $sources, array $options): OnboardingCollection
    {
        $items = new OnboardingCollection();

        if (in_array('json', $sources, true)) {
            $items = $items->merge((new ImportFromJson())->loadData('test-data/export.json'));
        }

        if (in_array('csv', $sources, true)) {
            // Set the template style to define the csv fields
            $data = new ImportFromCsv($options);

            if (array_key_exists('template', $options)) {
                $data->setTemplate($options['template']);
            }

            if (array_key_exists('csv_skip_header', $options)) {
                $data->skipFirstRow(array_key_exists('csv_skip_header', $options) && $options['csv_skip_header']);
            }

            $data = $data->loadData('test-data/export.tsv');

            $items = $items->merge($data);
        }

        return $items;
    }
}
