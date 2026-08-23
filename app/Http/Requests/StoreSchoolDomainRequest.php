<?php

namespace App\Http\Requests;

use App\Models\SchoolDomain;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSchoolDomainRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'host' => ['required', 'string', 'max:253', Rule::unique('school_domains', 'host')],

            // A campus of this organization, or nothing for the whole of it.
            'school_id' => [
                'nullable',
                Rule::exists('schools', 'id')->where('organization_id', $this->route('organization')?->id),
            ],
            'is_primary' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get the values the rules read, tidied the way they are stored.
     *
     * @return array<string, mixed>
     */
    public function validationData(): array
    {
        $data = parent::validationData();

        if (isset($data['host']) && is_string($data['host'])) {
            $data['host'] = SchoolDomain::tidy($data['host']);
        }

        return $data;
    }

    /**
     * Get the messages for the rules above.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'host.unique' => 'That web address is already claimed.',
            'school_id.exists' => 'That campus belongs to another organization.',
        ];
    }
}
