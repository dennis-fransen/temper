<?php

declare(strict_types=1);

namespace App\Temper\Statistics\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OnboardingResourceCollection extends ResourceCollection
{
    public $collects = OnboardingResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->values(),
        ];
    }
}
