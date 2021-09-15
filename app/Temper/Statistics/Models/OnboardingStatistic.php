<?php

declare(strict_types=1);

namespace App\Temper\Statistics\Models;

class OnboardingStatistic
{
    public function __construct(array $elements)
    {
        $this->user_id = (int) $elements['user_id'];
        $this->created_at = $elements['created_at'];
        $this->onboarding_perentage = (int) $elements['onboarding_perentage'];
        $this->count_applications = (int) $elements['count_applications'];
        $this->count_accepted_applications = (int) $elements['count_accepted_applications'];
    }
}
