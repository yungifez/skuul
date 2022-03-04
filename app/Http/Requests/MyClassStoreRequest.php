<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MyClassStoreRequest extends FormRequest
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
            'class_group_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'class_group_id.required' => 'Please select a class group',
        ];
    }
}
