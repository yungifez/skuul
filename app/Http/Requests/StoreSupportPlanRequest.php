<?php

namespace App\Http\Requests;

use App\Enums\SupportCategory;
use App\Models\StudentRecord;
use App\Models\SupportPlan;
use App\Traits\ValidatesSchoolMembership;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupportPlanRequest extends FormRequest
{
    use ValidatesSchoolMembership;

    /**
     * Determine whether the person may open a plan.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', SupportPlan::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'student_record_id' => ['required', 'integer', Rule::exists((new StudentRecord)->getTable(), 'id')->where('school_id', current_school_id())],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::enum(SupportCategory::class)],
            'summary' => ['nullable', 'string', 'max:5000'],
            'starts_on' => ['nullable', 'date'],
            'review_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'assigned_to' => [
                'nullable',
                'integer',
                $this->memberOfWorkingSchool(),
            ],
        ];
    }

    /**
     * Get the messages for the rules that need plain wording.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'review_on.after_or_equal' => 'A plan cannot be reviewed before it starts.',
        ];
    }
}
