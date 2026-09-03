<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class UpdateDormitoryRequest extends FormRequest
{
    /**
     * Get the rules for changing a boarding house.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:100',
                ValidationRule::unique('dormitories', 'name')
                    ->where('school_id', current_school_id())
                    ->ignore($this->route('dormitory')?->id),
            ],
            'label' => 'required|string|max:40',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Get the messages staff read when a field is wrong.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'This campus already has a house with that name.',
        ];
    }
}
