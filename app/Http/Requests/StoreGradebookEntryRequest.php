<?php

namespace App\Http\Requests;

use App\Enums\GradeEntryState;
use App\Models\CourseOffering;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGradebookEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $courseOffering = $this->route('courseOffering');

        return $courseOffering instanceof CourseOffering
            && ($this->user()?->can('manageGradebook', $courseOffering) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'grade_item_id' => ['required', 'integer', Rule::exists('grade_items', 'id')],
            'student_record_id' => ['required', 'integer', Rule::exists('student_records', 'id')],
            'state' => ['required', Rule::enum(GradeEntryState::class)],
            'points' => ['nullable', 'numeric', 'min:0'],
            'grading_scale_option_id' => ['nullable', 'integer', Rule::exists('grading_scale_options', 'id')],
            'comment' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
