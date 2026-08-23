<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBillingGroupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('billing_groups', 'name')->where('organization_id', $this->route('organization')?->id),
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
        return ['name.unique' => 'This organization already has a group with that name.'];
    }
}
