<?php

declare(strict_types=1);

namespace App\Temper\Statistics\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OnboardingStatisticsListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'sort' => 'array',
            'filter' => 'array',
        ];
    }
}
