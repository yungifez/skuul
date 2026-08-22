<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentPromoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'source_academic_cycle_section_id'      => ['required', 'integer', Rule::exists('academic_cycle_sections', 'id')->where('school_id', current_school_id())],
            'destination_academic_cycle_section_id' => ['required', 'integer', 'different:source_academic_cycle_section_id', Rule::exists('academic_cycle_sections', 'id')->where('school_id', current_school_id())],
            'student_id'                            => ['required', 'array', 'min:1'],
            'student_id.*'                          => ['integer', Rule::exists('users', 'id')],
        ];
    }
}
