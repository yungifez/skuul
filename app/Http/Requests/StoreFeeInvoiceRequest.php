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
            'due_date' => 'required|date|after_or_equal:issue_date',
            'note' => 'nullable|max:10000',
            'student_records' => 'required|array',
            'student_records.*' => [
                'integer',
                Rule::exists('student_records', 'id')->where(function (Builder $query) {
                    return $query->where('school_id', current_school_id())
                        ->where('status', 'active');
                }),
            ],
            'records' => 'required|array',
            'records.*.fee_id' => 'required|integer|exists:fees,id',
            'records.*.amount' => 'required|integer|min:1',
            'records.*.waiver' => 'required|integer|min:0|lte:records.*.amount',
            'records.*.fine' => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'student_records.required' => 'No students added to this invoice',
            'records.required' => 'No Fees added to this invoice',
            'records.*.amount.required' => 'Amount not set',
            'records.*.waiver.required' => 'Waiver not set',
            'records.*.fine.required' => 'Fine not set',
            'records.*.amount.integer' => 'Amount must be a number',
            'records.*.amount.min' => 'Amount must be greater than or equeal to 1',
            'records.*.waiver.integer' => 'Waiver must be a number',
            'records.*.waiver.min' => 'Waiver must be greater than 0',
            'records.*.waiver.lte' => 'Waiver cannot be greater than the amount',
            'records.*.fine.integer' => 'Fine must be a number',
            'records.*.fine.min' => 'Fine must be greater than 0',
        ];
    }
}
