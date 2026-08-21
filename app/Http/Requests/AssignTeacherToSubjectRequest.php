<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignTeacherToSubjectRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'subjects'   => 'required|array',
            'subjects.*' => [
                'nullable',
                Rule::exists('subjects', 'id')->where('school_id', current_school_id()),
            ],
        ];
    }
}
