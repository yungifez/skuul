<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFeeRequest extends FormRequest
{
    /**
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
