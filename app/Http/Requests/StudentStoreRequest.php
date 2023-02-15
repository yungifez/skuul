<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'admission_number' => 'nullable|unique:student_records,admission_number',
            'admission_date'   => 'required|date',
            'my_class_id'      => 'required|exists:my_classes,id',
            'section_id'       => 'required|exists:sections,id',
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
            'section_id.required'  => 'Select a section',
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
            'section_id'  => 'section selection',
        ];
    }
}
