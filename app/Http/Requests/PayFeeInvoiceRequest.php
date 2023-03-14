<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayFeeInvoiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'pay' => 'required|integer|min:-10000000000000|max:10000000000000',
        ];
    }
}
