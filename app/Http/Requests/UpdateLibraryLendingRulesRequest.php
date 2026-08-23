<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLibraryLendingRulesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'loan_days' => 'required|integer|min:1|max:365',
            'learner_limit' => 'required|integer|min:1|max:100',
            'staff_limit' => 'required|integer|min:1|max:200',
            'renewals_allowed' => 'required|integer|min:0|max:10',
            'fine_per_day' => 'required|numeric|min:0|max:100000',
        ];
    }
}
