<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
                'max:255',
                Rule::unique('sections', 'name')->ignore($sectionId)->where('my_class_id', $myClassId),
            ],
        ];
    }
}
