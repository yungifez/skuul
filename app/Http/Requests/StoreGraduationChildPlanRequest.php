<?php

namespace App\Http\Requests;

use App\Models\GraduationPlan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGraduationChildPlanRequest extends FormRequest
{
    /**
     * Determine whether the person may add a stage below this plan.
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
                Rule::unique((new GraduationPlan)->getTable(), 'name')->where('school_id', current_school_id()),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'completion_operator' => ['required', Rule::in(['all', 'any', 'at_least', 'at_least_credits'])],
            'required_count' => ['nullable', 'integer', 'min:1', 'max:1000', 'required_if:completion_operator,at_least'],
            'required_credits' => ['nullable', 'integer', 'min:1', 'max:1000', 'required_if:completion_operator,at_least_credits'],
            'position' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'is_negated' => ['required', 'boolean'],
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
        ];
    }
}
