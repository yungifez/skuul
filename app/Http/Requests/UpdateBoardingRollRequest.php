<?php

namespace App\Http\Requests;

use App\Enums\BoardingRollEntryStatus;
use App\Models\BoardingRoll;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBoardingRollRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $roll = $this->route('boardingRoll');

        return $this->user()?->can('manage boarding') === true
            && $roll instanceof BoardingRoll
            && $roll->school_id === current_school_id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'entries' => ['required', 'array'],
            'entries.*.id' => ['required', 'integer'],
            'entries.*.status' => ['required', Rule::in(BoardingRollEntryStatus::values())],
            'entries.*.location' => ['nullable', 'string', 'max:150'],
            'entries.*.note' => ['nullable', 'string', 'max:1000'],
            'complete' => ['required', 'boolean'],
        ];
    }
}
