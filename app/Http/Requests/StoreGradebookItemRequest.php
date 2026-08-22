<?php

namespace App\Http\Requests;

use App\Enums\GradeItemType;
use App\Models\CourseOffering;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGradebookItemRequest extends FormRequest
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
        $courseOffering = $this->route('courseOffering');
        $courseOfferingId = $courseOffering instanceof CourseOffering ? $courseOffering->id : null;

        return [
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', Rule::enum(GradeItemType::class)],
            'grade_category_id' => [
                'nullable',
                'integer',
                Rule::exists('grade_categories', 'id')->where('course_offering_id', $courseOfferingId),
            ],
            'max_points' => ['nullable', 'numeric', 'gt:0', 'max:999999.99'],
            'weight' => ['required', 'numeric', 'gt:0', 'max:999999.999'],
            'due_on' => ['nullable', 'date'],
        ];
    }
}
