<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeSystemRequest extends FormRequest
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
            'name'           => 'required|string',
            'remark'         => 'nullable|string',
            'class_group_id' => 'required|integer|exists:class_groups,id',
        ];
    }
}
