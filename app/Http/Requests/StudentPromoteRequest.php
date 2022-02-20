<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'old_class' => 'required|exists:my_classes,id',
            'new_class' => 'required|exists:my_classes,id',
            'old_section' => 'required|exists:sections,id',
            'new_section' => 'required|exists:sections,id',
            'student_id.*' => 'nullable|exists:users,id',
        ];
    }
}
