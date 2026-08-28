<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFeeInvoiceRecordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|integer|min:1',
            'waiver' => 'nullable|integer|min:0|lte:amount',
            'fine' => 'nullable|integer|min:0',
        ];
    }
}
