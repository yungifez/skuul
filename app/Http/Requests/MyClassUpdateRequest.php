<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class MyClassUpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => [
                "required", 
                Rule::unique('my_classes','name')->ignore($this->route()->parameter('class')->id),
            ],
            'class_group_id' => 'required'
        ];
    }
}
