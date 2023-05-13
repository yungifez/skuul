<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolStoreRequest extends FormRequest
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
            'phone'    => 'nullable|max:255|regex:/^([0-9\s\-\+\(\)]*)$/|min:5',
            'email'    => 'nullable|email|max:511',
            'initials' => 'nullable|max:10|string',
            'logo'     => ['nullable', 'image', 'max:3000'],
        ];
    }
}
