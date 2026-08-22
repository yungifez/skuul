<?php

namespace App\Http\Requests;

use App\Traits\ValidatesSchoolMembership;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupportPlanActionRequest extends FormRequest
{
    use ValidatesSchoolMembership;

    /**
     * Determine whether the person may work on this plan.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('supportPlan')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:1000'],
            'due_on' => ['nullable', 'date'],
            'assigned_to' => [
                'nullable',
                'integer',
                $this->memberOfWorkingSchool(),
            ],
        ];
    }
}
