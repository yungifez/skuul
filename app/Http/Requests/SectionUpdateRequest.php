<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SectionUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $sectionId = $this->route()->parameter('section')->id;
        $myClassId = $this->route()->parameter('section')->my_class_id;
        return [
            'name' => [
                'required',
                Rule::unique('sections','name')->ignore($sectionId)->where("my_class_id", $myClassId )
            ]
        ];
    }
}
