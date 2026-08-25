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
            'organization_id' => ['required', 'exists:organizations,id'],
            'name' => 'required|max:255',
            'address' => 'required|string|min:8|max:255',
            'country' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:30',
            'phone' => 'nullable|max:255|regex:/^([0-9\s\-\+\(\)]*)$/|min:5',
            'email' => 'nullable|email|max:511',
            'initials' => 'nullable|max:10|string',
            'logo' => ['nullable', 'image', 'max:3000'],
        ];
    }
}
