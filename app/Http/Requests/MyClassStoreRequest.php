<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
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
        $classGroupId = $this->get('class_group_id');

        
        return [
            'name' => [
                "required", 
                //checks if there is a class with a name in class group
                Rule::unique('my_classes','name')->where(fn ($query) => $query->where('class_group_id', $classGroupId)),
            ],
            'class_group_id' => 'required|exists:class_groups,id',
        ];
    }

    public function messages()
    {
        return [
            'class_group_id.required' => 'Please select a class group',
        ];
    }
}
