<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class StoreDormitoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:100',
                ValidationRule::unique('dormitories', 'name')->where('school_id', current_school_id()),
            ],
            'label' => 'required|string|max:40',
            'notes' => 'nullable|string|max:1000',
            'rooms' => 'required|integer|min:1|max:200',
            'beds_per_room' => 'required|integer|min:1|max:40',
        ];
    }

    /**
     * Get the messages the office reads when a field is wrong.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'This campus already has a house with that name.',
            'rooms.required' => 'Say how many rooms the house has.',
            'beds_per_room.required' => 'Say how many beds are in each room.',
        ];
    }
}
