<?php

namespace App\Http\Requests;

use App\Models\Cohort;
use App\Models\GraduationPlan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGraduationPlanRequest extends FormRequest
{
    /**
     * Determine whether the person may write a plan.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', GraduationPlan::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique((new GraduationPlan)->getTable(), 'name')->where('school_id', current_school_id()),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'completion_operator' => ['sometimes', Rule::in(['all', 'any', 'at_least', 'at_least_credits'])],
            'required_count' => ['nullable', 'integer', 'min:1', 'max:1000', 'required_if:completion_operator,at_least'],
            'uses_credits' => ['required', 'boolean'],
            'required_credits' => ['nullable', 'integer', 'min:1', 'max:1000', 'required_if:uses_credits,1', 'required_if:completion_operator,at_least_credits'],
            'cohort_id' => ['nullable', 'integer', Rule::exists((new Cohort)->getTable(), 'id')->where('school_id', current_school_id())],
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
            'name.unique' => 'This school already has a plan with that name.',
            'required_credits.required_if' => 'A plan that counts credits must say how many are needed.',
        ];
    }
}
