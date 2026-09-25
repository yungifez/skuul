<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class StoreFeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return self::feeRulesForCurrentSchool();
    }

    /** @return array<string, string> */
    public static function feeRules(): array
    {
        return [
            'name' => 'required|max:1024',
            'description' => 'nullable|max:10000',
        ];
    }

    /** @return array<string, array<int, mixed>> */
    public static function feeRulesForCurrentSchool(): array
    {
        return self::feeRules() + [
            'fee_category_id' => ['required', 'integer', ValidationRule::exists('fee_categories', 'id')->where('school_id', current_school_id())],
        ];
    }
}
