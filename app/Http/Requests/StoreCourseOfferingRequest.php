<?php

namespace App\Http\Requests;

use App\Enums\RosterMode;
use App\Models\CourseOffering;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseOfferingRequest extends FormRequest
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
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'academic_year_id'             => ['required', 'integer', Rule::exists('academic_years', 'id')->where('school_id', current_school_id())],
            'academic_period_id'           => ['required', 'integer', Rule::exists('academic_periods', 'id')->where('school_id', current_school_id())],
            'subject_id'                   => ['required', 'integer', Rule::exists('subjects', 'id')->where('school_id', current_school_id())],
            'academic_level_id'            => ['required', 'integer', Rule::exists('academic_levels', 'id')->where('school_id', current_school_id())],
            'roster_mode'                  => ['required', Rule::enum(RosterMode::class)],
            'academic_cycle_section_ids'   => ['nullable', 'array'],
            'academic_cycle_section_ids.*' => ['integer', 'distinct', Rule::exists('academic_cycle_sections', 'id')->where('school_id', current_school_id())],
            'student_record_ids'           => ['nullable', 'array'],
            'student_record_ids.*'         => ['integer', 'distinct', Rule::exists('student_records', 'id')],
            'planned_periods_per_week'     => ['nullable', 'integer', 'min:1', 'max:80'],
            'capacity'                     => ['nullable', 'integer', 'min:1', 'max:5000'],
        ];
    }
}
