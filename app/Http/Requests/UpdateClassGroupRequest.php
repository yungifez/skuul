<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClassGroupRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'school_id' => auth()->user()->school_id,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('class_groups')->ignore($this->route()->parameter('class_group')->id)->where(fn ($query) => $query->where('school_id', $this->input('school_id') ?? auth()->user()->school_id)),
            ],
        ];
    }
}
