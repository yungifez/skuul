<?php

namespace App\Http\Requests;

use App\Enums\GradeItemType;
use App\Models\CourseOffering;
use App\Models\GradeItem;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGradebookItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $courseOffering = $this->route('courseOffering');
        $gradeItem = $this->route('gradeItem');

        return $courseOffering instanceof CourseOffering
            && $gradeItem instanceof GradeItem
            && $gradeItem->course_offering_id === $courseOffering->id
            && ($this->user()?->can('manageGradebook', $courseOffering) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $courseOffering = $this->route('courseOffering');
        $gradeItem = $this->route('gradeItem');
        $courseOfferingId = $courseOffering instanceof CourseOffering ? $courseOffering->id : null;

        return [
            'name' => ['required', 'string', 'max:150'],
            'grade_category_id' => [
                'nullable',
                'integer',
                Rule::exists('grade_categories', 'id')->where('course_offering_id', $courseOfferingId),
            ],
            'max_points' => [
                Rule::requiredIf(fn (): bool => $gradeItem instanceof GradeItem && $gradeItem->type === GradeItemType::Numeric),
                'nullable',
                'numeric',
                'gt:0',
                'max:999999.99',
            ],
            'weight' => ['required', 'numeric', 'gt:0', 'max:999999.999'],
            'due_on' => ['nullable', 'date'],
        ];
    }
}
