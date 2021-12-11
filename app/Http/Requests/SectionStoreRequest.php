<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'name' => 'required',
            'my_class_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'my_class_id.required' => 'Please select a class'
        ];
    }
}
