<?php

namespace App\Http\Requests;

use App\Models\StudentRecord;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGraduationExemptionRequest extends FormRequest
{
    /**
     * Determine whether the person may change this plan.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('graduationPlan')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'graduation_requirement_id' => [
                'required',
                'integer',
                // The requirement must belong to the plan in the address, so a
                // form cannot excuse a learner from another school's plan.
                Rule::exists('graduation_requirements', 'id')
                    ->where('graduation_plan_id', $this->route('graduationPlan')->id),
            ],
            'student_record_id' => ['required', 'integer', Rule::exists((new StudentRecord)->getTable(), 'id')->where('school_id', current_school_id())],
            'reason' => ['required', 'string', 'max:500'],
        ];
    }
}
