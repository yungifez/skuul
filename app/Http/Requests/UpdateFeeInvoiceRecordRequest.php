<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFeeInvoiceRecordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|integer|min:1',
            'waiver' => 'nullable|integer|min:0|lt:amount',
            'fine'   => 'nullable|integer|min:0',
        ];
    }
}
