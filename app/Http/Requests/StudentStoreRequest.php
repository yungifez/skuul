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
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'max:100'],
            'birthday' => ['required', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:100'],
            'profile_photo' => ['nullable', 'image', 'max:3000'],
            'admission_number' => [
                'nullable',
                Rule::unique('student_records', 'admission_number')->where(fn ($query) => $query->where('school_id', current_school_id())),
            ],
            'admission_date' => 'required|date',
            'my_class_id' => 'required|exists:my_classes,id',
            'section_id' => 'required|exists:sections,id',
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
            'my_class_id.required' => 'Select a class',
            'section_id.required' => 'Select a section',
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
            'my_class_id' => 'class selection',
            'section_id' => 'section selection',
        ];
    }
}
