<?php

declare(strict_types=1);

namespace App\Temper\Statistics\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OnboardingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'user_id' => $this->user_id,
            'created_at' => $this->created_at->format('Y-m-d'),
            'onboarding_perentage' => $this->onboarding_perentage,
            'count_applications' => $this->count_applications,
            'count_accepted_applications' => $this->count_accepted_applications,
        ];
    }
}
