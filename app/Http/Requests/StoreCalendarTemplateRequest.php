<?php

namespace App\Http\Requests;

use App\Enums\AcademicPeriodType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCalendarTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manageCalendar', $this->route('organization')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'cycle_length_days' => ['required', 'integer', 'min:1', 'max:3660'],
            'is_default' => ['nullable', 'boolean'],
            'auto_open' => ['nullable', 'boolean'],
            'generate_ahead_weeks' => ['nullable', 'integer', 'min:0', 'max:104'],
            'remind_days_before' => ['nullable', 'integer', 'min:0', 'max:90'],
            'periods' => ['required', 'array', 'min:1', 'max:12'],
            'periods.*.name' => ['nullable', 'string', 'max:100'],
            'periods.*.label' => ['nullable', 'string', 'max:100'],
            'periods.*.type' => ['nullable', Rule::in(AcademicPeriodType::values())],
            'periods.*.position' => ['nullable', 'integer', 'min:1', 'max:99'],
            'periods.*.start_offset_days' => ['nullable', 'integer', 'min:0', 'max:3660'],
            'periods.*.length_days' => ['nullable', 'integer', 'min:1', 'max:3660'],
            'periods.*.parent_index' => ['nullable', 'integer', 'min:1', 'max:12'],
        ];
    }
}
