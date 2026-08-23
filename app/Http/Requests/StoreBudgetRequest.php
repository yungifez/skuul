<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class StoreBudgetRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        $ofThisSchool = fn (Builder $query) => $query->where('school_id', current_school_id());

        return [
            'academic_year_id' => [
                'required', 'integer',
                ValidationRule::exists('academic_years', 'id')->where($ofThisSchool),
            ],
            'academic_period_id' => [
                'nullable', 'integer',
                ValidationRule::exists('academic_periods', 'id')->where($ofThisSchool),
            ],
            'ledger_account_id' => [
                'required', 'integer',
                ValidationRule::exists('ledger_accounts', 'id')->where($ofThisSchool),
            ],
            'program_id' => [
                'nullable', 'integer',
                ValidationRule::exists('programs', 'id')->where($ofThisSchool),
            ],
            'fund' => 'nullable|string|max:60',
            'amount' => 'required|numeric|min:0|max:1000000000',
            'note' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get the messages the office reads when a field is wrong.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ledger_account_id.required' => 'Say which account the plan is about.',
            'amount.required' => 'Say how much the account is allowed.',
        ];
    }
}
