<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
            'locale' => ['nullable', 'string', Rule::in(array_keys(config('app.supported_locales', [])))],
            'school_language_preset' => ['nullable', 'string', 'in:home_sections,subject_schedule,hybrid'],
            'organization_name' => ['required', 'string', 'max:255'],
            'campus_name' => ['required', 'string', 'max:255'],
            'campus_address' => ['required', 'string', 'max:255'],
            'campus_country' => ['required', 'string', 'max:100'],
            'campus_state' => ['required', 'string', 'max:100'],
            'campus_city' => ['required', 'string', 'max:100'],
            'campus_postal_code' => ['required', 'string', 'max:30'],
            'campus_initials' => ['nullable', 'string', 'max:10'],
            'campus_email' => ['nullable', 'email:rfc', 'max:255'],
            'load_demo_data' => ['sometimes', 'boolean'],
        ];
    }
}
