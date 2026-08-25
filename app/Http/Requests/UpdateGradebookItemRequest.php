<?php

namespace App\Http\Requests;

use App\Models\CourseOffering;
use App\Models\ExamSlot;
use App\Models\GradeItem;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
        $courseOfferingId = $courseOffering instanceof CourseOffering ? $courseOffering->id : null;

        return [
            'name' => ['required', 'string', 'max:150'],
            'grade_category_id' => [
                'nullable',
                'integer',
                Rule::exists('grade_categories', 'id')->where('course_offering_id', $courseOfferingId),
            ],
            'exam_slot_id' => ['nullable', 'integer', Rule::exists((new ExamSlot)->getTable(), 'id')],
            'max_points' => ['nullable', 'numeric', 'gt:0', 'max:999999.99'],
            'weight' => ['required', 'numeric', 'gt:0', 'max:999999.999'],
            'due_on' => ['nullable', 'date'],
        ];
    }

    /**
     * Ensure a linked paper belongs to this course offering's reporting period.
     */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $courseOffering = $this->route('courseOffering');
            $examSlotId = $this->integer('exam_slot_id');

            if (!$courseOffering instanceof CourseOffering || $examSlotId === 0) {
                return;
            }

            if (!ExamSlot::query()->whereKey($examSlotId)->whereRelation('exam', 'academic_period_id', $courseOffering->academic_period_id)->exists()) {
                $validator->errors()->add('exam_slot_id', 'Choose an exam paper from this reporting period.');
            }
        }];
    }
}
