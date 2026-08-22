<?php

namespace App\Http\Requests;

use App\Traits\ValidatesSchoolMembership;
use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentActionRequest extends FormRequest
{
    use ValidatesSchoolMembership;

    /**
     * Determine whether the person may work on this case.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('incident')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:100'],
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
