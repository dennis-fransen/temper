<?php

declare(strict_types=1);

namespace App\Temper\Statistics;

use App\Temper\Statistics\Collections\OnboardingCollection;
use App\Temper\Statistics\Requests\OnboardingStatisticsListRequest;
use App\Temper\Statistics\Resources\OnboardingResourceCollection;
use App\Temper\Statistics\Services\OnboardingDataCollector;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use League\Flysystem\FileNotFoundException;

class OnboardingController
{
    private OnboardingDataCollector $onboardingDataCollector;

    public const FILTER_FIELDS = ['created_at', 'onboarding_perentage', 'user_id', 'count_applications', 'count_accepted_applications'];

    public function __construct(OnboardingDataCollector $onboardingDataCollector)
    {
        $this->onboardingDataCollector = $onboardingDataCollector;
    }

    /**
     * @throws FileNotFoundException
     * @throws \JsonException
     */
    public function index(OnboardingStatisticsListRequest $request): JsonResponse
    {
        $diff = (array_diff_key($request->all(), $request->validated()));
        if ($diff) {
            return new JsonResponse([
                'error' => [
                    'message' => 'invalid parameters',
                    'fields' => implode(array_keys($diff)),
                ],
            ], 422);
        }

        // Example where we use the header in the csv ( TSV? )
//        $data = $this->onboardingDataCollector->gatherData(['json', 'csv'], [
//            'csv_skip_header' => false,
//        ]);

        // If we have a csv without header, we can build our own template
        $fieldTemplate = [
            'user_id',
            'created_at',
            'onboarding_perentage',
            'count_applications',
            'count_accepted_applications'
        ];

        $data = $this->onboardingDataCollector->gatherData(['json', 'csv'], [
            'csv_skip_header' => true,
            'template' => $fieldTemplate,
        ]);

        try {
            $data = $this->applyFilters($request, $data);
            $data = $this->applySorters($request, $data);
        } catch (\Exception $ex) {
            return new JsonResponse([
                'error' => [
                    'message' => $ex->getMessage(),
                ],
            ], 422);
        }

        return new JsonResponse(new OnboardingResourceCollection($data));
    }

    /**
     * @throws \Exception
     */
    protected function applyFilters(OnboardingStatisticsListRequest $request, mixed $data): mixed
    {

        if ($request->get('filter')) {
            foreach ($request->get('filter') as $filterCol => $parameter) {

                // Check if filter is valid
                if (!in_array($filterCol, self::FILTER_FIELDS)) {
                    throw new \Exception('Invalid filter, use one off:' . implode(', ', self::FILTER_FIELDS));
                }

                // Make created at a carbon object to cleanly compare it to our stored created_at value
                if ('created_at' === $filterCol) {
                    $parameter = new Carbon($parameter);
                }

                $data = $data->where($filterCol, $parameter);
            }
        }

        return $data;
    }

    /**
     * @throws \Exception
     */
    protected function applySorters(OnboardingStatisticsListRequest $request, OnboardingCollection $data): OnboardingCollection
    {
        if ($request->get('sort')) {
            foreach ($request->get('sort') as $sortCol => $direction) {
                if ('asc' === $direction) {
                    $data = $data->sortBy($sortCol);
                } elseif ('desc' === $direction) {
                    $data = $data->sortByDesc($sortCol);
                } else {
                    throw new \Exception('Invalid sort direction given, please use "asc" or "desc"');
                }
            }
        }

        return $data;
    }
}
