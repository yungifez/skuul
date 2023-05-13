<?php

namespace App\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeeInvoiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'issue_date' => 'required|date',
            'due_date'   => 'required|date|after_or_equal:issue_date',
            'note'       => 'nullable|max:10000',
            'users'      => 'required|array',
            'users.*'    => [
                'integer',
                Rule::exists('users', 'id')->where(function (Builder $query) {
                    return $query->where('school_id', auth()->user()->school->id);
                }),
            ],
            'records'          => 'required|array',
            'records.*.fee_id' => 'required|integer|exists:fees,id',
            'records.*.amount' => 'required|integer|min:1',
            'records.*.waiver' => 'required|integer|min:0|lt:records.*.amount',
            'records.*.fine'   => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'users.required'            => 'No users added to this invoice',
            'records.required'          => 'No Fees added to this invoice',
            'records.*.amount.required' => 'Amount not set',
            'records.*.waiver.required' => 'Waiver not set',
            'records.*.fine.required'   => 'Fine not set',
            'records.*.amount.integer'  => 'Amount must be a number',
            'records.*.amount.min'      => 'Amount must be greater than or equeal to 1',
            'records.*.waiver.integer'  => 'Waiver must be a number',
            'records.*.waiver.min'      => 'Waiver must be greater than 0',
            'records.*.waiver.lt'       => 'Waiver for  must be less than amount',
            'records.*.fine.integer'    => 'Fine must be a number',
            'records.*.fine.min'        => 'Fine must be greater than 0',
        ];
    }
}
