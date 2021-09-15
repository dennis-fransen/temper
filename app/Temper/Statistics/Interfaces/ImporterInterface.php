<?php

declare(strict_types=1);

namespace App\Temper\Statistics\Interfaces;

use App\Temper\Statistics\Collections\OnboardingCollection;

interface ImporterInterface
{
    public function loadData(string $source): OnboardingCollection;
}
