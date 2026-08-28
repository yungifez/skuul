<?php

namespace App\Http\Requests;

use App\Services\Finance\PaymentChannelRegistry;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create expense') === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'expense_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:1000000000'],
            'ledger_account_id' => [
                'required', 'integer',
                Rule::exists('ledger_accounts', 'id')->where(fn (Builder $query) => $query
                    ->where('school_id', current_school_id())
                    ->where('type', 'expense')
                    ->where('is_active', true)),
            ],
            'method' => ['required', 'string', Rule::in(app(PaymentChannelRegistry::class)->keys())],
            'vendor' => ['nullable', 'string', 'max:150'],
            'reference' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:2000'],
            'program_id' => [
                'nullable', 'integer',
                Rule::exists('programs', 'id')->where(fn (Builder $query) => $query->where('school_id', current_school_id())),
            ],
            'fund' => ['nullable', 'string', 'max:60'],
        ];
    }
}
