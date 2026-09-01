<?php

namespace App\Http\Requests;

use App\Models\Cohort;
use App\Models\GraduationPlan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGraduationPlanRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique((new GraduationPlan)->getTable(), 'name')
                    ->where('school_id', current_school_id())
                    ->ignore($this->route('graduationPlan')),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'completion_operator' => ['sometimes', Rule::in(['all', 'any'])],
            'uses_credits' => ['required', 'boolean'],
            'required_credits' => ['nullable', 'integer', 'min:1', 'max:1000', 'required_if:uses_credits,1'],
            'cohort_id' => ['nullable', 'integer', Rule::exists((new Cohort)->getTable(), 'id')->where('school_id', current_school_id())],
            'is_active' => ['required', 'boolean'],
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
