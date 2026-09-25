<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFeeCategoryRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'school_id' => current_school_id(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return StoreFeeCategoryRequest::feeCategoryRules() + [
            'school_id' => 'required|integer|exists:schools,id',
        ];
    }
}
