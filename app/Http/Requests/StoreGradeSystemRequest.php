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
            'grade_from' => 'required|numeric',
            'grade_to' => 'required|numeric',
            'name' => 'required|string',
            'remark' => 'string',
            'class_group_id' => 'required|integer|exists:class_groups,id'
        ];
    }
}
