<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeeInvoiceRecordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'fee_invoice_id' => [
                'required',
                'integer',
                'exists:fee_invoices,id',
            ],
            'fee_id' => 'required|integer|exists:fees,id',
            'amount' => 'required|integer|min:1',
            'waiver' => 'nullable|integer|min:0|lt:amount',
            'fine'   => 'nullable|integer|min:0',
        ];
    }
}
