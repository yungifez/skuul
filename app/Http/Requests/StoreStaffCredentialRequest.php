<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffCredentialRequest extends FormRequest
{
    /**
     * Determine whether the person may change this employment record.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('staffProfile')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:150'],
            'issuer' => ['nullable', 'string', 'max:150'],
            'reference' => ['nullable', 'string', 'max:100'],
            'issued_on' => ['nullable', 'date'],
            'expires_on' => ['nullable', 'date', 'after_or_equal:issued_on'],
        ];
    }

    /**
     * Get the messages for the rules that need plain wording.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'expires_on.after_or_equal' => 'A qualification cannot expire before it was issued.',
        ];
    }
}
