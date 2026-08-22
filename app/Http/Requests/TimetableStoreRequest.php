<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TimetableStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',
            'academic_cycle_section_id' => [
                'required',
                'integer',
                Rule::exists('academic_cycle_sections', 'id')
                    ->where('school_id', current_school_id())
                    ->where('academic_year_id', current_academic_year_id()),
            ],
        ];
    }
}
