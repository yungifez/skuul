<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'             => ['required', 'string', 'max:100'],
            'email'            => ['required', 'email:rfc,dns', 'max:100'],
            'birthday'         => ['required', 'date', 'before:today'],
            'gender'           => ['nullable', 'string', 'max:100'],
            'nationality'      => ['nullable', 'string', 'max:100'],
            'state'            => ['nullable', 'string', 'max:100'],
            'city'             => ['nullable', 'string', 'max:100'],
            'phone'            => ['nullable', 'string', 'max:100'],
            'address'          => ['nullable', 'string', 'max:100'],
            'profile_photo'    => ['nullable', 'image', 'max:3000'],
            'admission_number' => [
                'nullable',
                Rule::unique('student_records', 'admission_number')->where(fn ($query) => $query->where('school_id', current_school_id())),
            ],
            'admission_date'            => 'required|date',
            'academic_cycle_section_id' => [
                'required',
                'integer',
                Rule::exists('academic_cycle_sections', 'id')->where('school_id', current_school_id()),
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'academic_cycle_section_id.required' => 'Select a home section',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'academic_cycle_section_id' => 'home section',
        ];
    }
}
