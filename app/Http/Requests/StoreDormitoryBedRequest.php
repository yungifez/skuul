<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class StoreDormitoryBedRequest extends FormRequest
{
    /**
     * Get the rules for adding a bed.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        $room = $this->route('room');

        return [
            'name' => [
                'required', 'string', 'max:40',
                ValidationRule::unique('dormitory_beds', 'name')
                    ->where('dormitory_room_id', $room?->id),
            ],
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
            'name.unique' => 'This room already has a bed with that name.',
        ];
    }
}
