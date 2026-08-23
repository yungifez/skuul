<?php

namespace App\Http\Requests;

use App\Enums\FacilityKind;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class StoreFacilityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:120',
                ValidationRule::unique('facilities', 'name')
                    ->where('school_id', current_school_id())
                    ->ignore($this->route('facility')?->id),
            ],
            'kind' => ['required', ValidationRule::in(FacilityKind::values())],
            'capacity' => 'nullable|integer|min:1|max:100000',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get the messages staff read when a field is wrong.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'This campus already shares something with that name.',
        ];
    }
}
