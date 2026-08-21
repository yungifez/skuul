<?php

namespace App\Http\Requests;

use App\Services\Report\ReportRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRunRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(array_keys(app(ReportRegistry::class)->all()))],
            'parameters' => ['nullable', 'array'],
        ];
    }
}
