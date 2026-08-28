<?php

namespace App\Http\Requests;

use App\Enums\RosterMode;
use App\Models\CourseOffering;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseOfferingRosterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $courseOffering = $this->route('courseOffering');

        return $courseOffering instanceof CourseOffering
            && ($this->user()?->can('update', $courseOffering) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * The academic level is inferred from the selected sections or learners.
     * Whole-level rosters provide it explicitly because they select neither.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'roster_mode' => ['required', Rule::enum(RosterMode::class)],
            'academic_level_id' => ['nullable', 'integer', Rule::exists('academic_levels', 'id')->where('school_id', current_school_id())],
            'academic_cycle_section_ids' => ['nullable', 'array'],
            'academic_cycle_section_ids.*' => ['integer', 'distinct', Rule::exists('academic_cycle_sections', 'id')->where('school_id', current_school_id())],
            'student_record_ids' => ['nullable', 'array'],
            'student_record_ids.*' => ['integer', 'distinct', Rule::exists('student_records', 'id')->where('school_id', current_school_id())],
        ];
    }
}
