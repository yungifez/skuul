<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'     => 'required|max:255',
            'address'  => 'required|min:8|max:1000',
            'phone'    => 'nullable|max:255|min:5|regex:/^([0-9\s\-\+\(\)]*)$/',
            'email'    => 'nullable|email|max:255',
            'initials' => 'nullable|string|max:10',
            'logo'     => ['nullable', 'image', 'max:3000'],
        ];
    }
}
