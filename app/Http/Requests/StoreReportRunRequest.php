<?php

namespace App\Http\Requests;

use App\Services\Report\ExportFormatRegistry;
use App\Services\Report\ReportRegistry;
use Illuminate\Database\Query\Builder;
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
            'format' => ['nullable', Rule::in(array_keys(app(ExportFormatRegistry::class)->all()))],
            'parameters' => ['nullable', 'array'],
            'parameters.financial_period_id' => [
                'nullable', 'integer',
                Rule::exists('financial_periods', 'id')->where(fn (Builder $query) => $query->where('school_id', current_school_id())),
            ],
        ];
    }
}
