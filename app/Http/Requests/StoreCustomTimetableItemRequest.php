<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomTimetableItemRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'school_id' => current_school_id(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('custom_timetable_items')->where(fn ($query) => $query->where('school_id', $this->input('school_id') ?? current_school_id())),
            ],
            'school_id' => [
                'required',
                'integer',
                'exists:schools,id',
            ],
        ];
    }
}
