<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeSystemRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'grade_from'     => 'required|numeric|gte:0|max:100',
            'grade_till'     => 'required|numeric|gt:grade_from|max:100',
            'name'           => 'required|string|max:255',
            'remark'         => 'nullable|string|max:255',
            'class_group_id' => 'required|integer|exists:class_groups,id',
        ];
    }
}
