<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MyClassUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $classGroupId = $this->get('class_group_id');
        $myClassId = $this->route()->parameter('class')->id;

        return [
            'name' => [
                'required',
                'max:255',
                //figure it out before changing
                Rule::unique('my_classes', 'name')->ignore($myClassId)->where(fn ($query) => $query->where('class_group_id', $classGroupId)),
            ],
            'class_group_id' => 'required|exists:class_groups,id',
        ];
    }
}
