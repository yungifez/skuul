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
            'admission_date'   => 'required|date|date_format:Y/m/d',
            'my_class_id'      => 'required|exists:my_classes,id',
            'section_id'       => 'required|exists:sections,id',
        ];
    }
}
