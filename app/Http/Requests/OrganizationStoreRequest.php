<?php

namespace App\Http\Requests;

use App\Enums\PlatformPermission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrganizationStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can(PlatformPermission::AccessAllOrganizations) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:255'],
            'code'    => ['nullable', 'string', 'max:50', 'alpha_dash', Rule::unique('organizations', 'code')],
            'address' => ['nullable', 'string', 'max:1000'],
            'email'   => ['nullable', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:255'],
        ];
    }
}
