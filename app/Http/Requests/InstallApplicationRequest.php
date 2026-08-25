<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class InstallApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'admin_name' => ['required', 'string', 'max:100'],
            'admin_email' => ['required', 'string', 'email:rfc', 'max:100'],
            'admin_password' => ['required', 'confirmed', Password::defaults()],
            'organization_name' => ['required', 'string', 'max:255'],
            'campus_name' => ['required', 'string', 'max:255'],
            'campus_address' => ['nullable', 'string', 'max:255'],
            'campus_address_line_2' => ['nullable', 'string', 'max:255'],
            'campus_country' => ['nullable', 'string', 'max:100'],
            'campus_state' => ['nullable', 'string', 'max:100'],
            'campus_city' => ['nullable', 'string', 'max:100'],
            'campus_postal_code' => ['nullable', 'string', 'max:30'],
            'campus_initials' => ['nullable', 'string', 'max:10'],
            'campus_email' => ['nullable', 'email:rfc', 'max:255'],
            'load_demo_data' => ['sometimes', 'boolean'],
        ];
    }
}
