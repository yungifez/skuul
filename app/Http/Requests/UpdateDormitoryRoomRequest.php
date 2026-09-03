<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class UpdateDormitoryRoomRequest extends FormRequest
{
    /**
     * Get the rules for changing a room.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        $room = $this->route('room');

        return [
            'name' => [
                'required', 'string', 'max:60',
                ValidationRule::unique('dormitory_rooms', 'name')
                    ->where('dormitory_id', $room?->dormitory_id)
                    ->ignore($room?->id),
            ],
            'floor' => 'nullable|string|max:40',
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
            'name.unique' => 'This house already has a room with that name.',
        ];
    }
}
