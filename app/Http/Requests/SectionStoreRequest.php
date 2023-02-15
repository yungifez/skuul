<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SectionStoreRequest extends FormRequest
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
        $myClassId = $this->input('my_class_id');

        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('sections', 'name')->where('my_class_id', $myClassId),
            ],
            'my_class_id' => [
                'required',
            ],
        ];
    }

    public function messages()
    {
        return [
            'my_class_id.required' => 'Please select a class',
        ];
    }
}
