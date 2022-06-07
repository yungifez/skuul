<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolUpdateRequest extends FormRequest
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
            'name'     => 'required|max:255',
            'address'  => 'required|min:8',
            'initials' => 'required|min:2',
            'phone'    => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:5',
        ];
    }
}
