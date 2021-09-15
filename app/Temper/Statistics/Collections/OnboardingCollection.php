<?php

declare(strict_types=1);

namespace App\Temper\Statistics\Collections;

use App\Temper\Statistics\Models\OnboardingStatistic;
use Illuminate\Support\Collection;

class OnboardingCollection extends Collection
{
    /**
     * Add an item to the collection.
     */
    public function addStatistic(OnboardingStatistic $item): self
    {
        $this->add($item);

        return $this;
    }
}
