<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFeeCategoryRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'school_id' => current_school_id(),
        ]);
    }

    /**
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return self::feeCategoryRules() + [
            'school_id' => 'required|integer|exists:schools,id',
        ];
    }

    /** @return array<string, string> */
    public static function feeCategoryRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',
        ];
    }
}
