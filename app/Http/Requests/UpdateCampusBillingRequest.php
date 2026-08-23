<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCampusBillingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // A group of this organization, or nothing to bill on its own.
            'billing_group_id' => [
                'nullable',
                Rule::exists('billing_groups', 'id')->where('organization_id', $this->route('organization')?->id),
            ],
        ];
    }

    /**
     * Get the messages for the rules above.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return ['billing_group_id.exists' => 'That group belongs to another organization.'];
    }
}
