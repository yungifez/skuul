<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return self::feeRules();
    }

    /** @return array<string, string> */
    public static function feeRules(): array
    {
        return StoreFeeRequest::feeRules();
    }
}
