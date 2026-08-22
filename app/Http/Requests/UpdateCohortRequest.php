<?php

namespace App\Http\Requests;

use App\Models\Cohort;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCohortRequest extends FormRequest
{
    /**
     * Determine whether the person may change this group.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('cohort')) ?? false;
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
                Rule::unique((new Cohort)->getTable(), 'name')
                    ->where('school_id', current_school_id())
                    ->ignore($this->route('cohort')),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
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
            'name.unique' => 'This school already has a group with that name.',
        ];
    }
}
