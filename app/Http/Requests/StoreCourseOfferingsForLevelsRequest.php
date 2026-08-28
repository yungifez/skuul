<?php

namespace App\Http\Requests;

use App\Enums\RosterMode;
use App\Models\AcademicPeriod;
use App\Models\CourseOffering;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseOfferingsForLevelsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', CourseOffering::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'academic_year_id' => ['required', 'integer', Rule::exists('academic_years', 'id')->where('school_id', current_school_id())],
            'academic_period_id' => ['required', function (string $attribute, mixed $value, Closure $fail): void {
                if ($value === 'all') {
                    return;
                }

                if (!filter_var($value, FILTER_VALIDATE_INT) || !AcademicPeriod::inSchool()->whereKey((int) $value)->exists()) {
                    $fail('The selected academic period is invalid.');
                }
            }],
            'subject_id' => ['required', 'integer', Rule::exists('subjects', 'id')->where('school_id', current_school_id())],
            'level_ids' => ['required', 'array', 'min:1'],
            'level_ids.*' => ['integer', 'distinct', Rule::exists('academic_levels', 'id')->where('school_id', current_school_id())->where('is_group', false)],
            'configurations' => ['required', 'array', 'min:1'],
            'configurations.*.academic_level_id' => ['required', 'integer', Rule::exists('academic_levels', 'id')->where('school_id', current_school_id())->where('is_group', false)],
            'configurations.*.roster_mode' => ['required', Rule::enum(RosterMode::class)],
            'configurations.*.academic_cycle_section_ids' => ['nullable', 'array'],
            'configurations.*.academic_cycle_section_ids.*' => ['integer', 'distinct', Rule::exists('academic_cycle_sections', 'id')->where('school_id', current_school_id())],
            'configurations.*.planned_periods_per_week' => ['nullable', 'integer', 'min:1', 'max:80'],
            'configurations.*.capacity' => ['nullable', 'integer', 'min:1', 'max:5000'],
        ];
    }
}
