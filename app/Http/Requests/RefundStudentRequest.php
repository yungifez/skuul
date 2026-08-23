<?php

namespace App\Http\Requests;

use App\Services\Finance\PaymentChannelRegistry;
use Brick\Money\Money as BrickMoney;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class RefundStudentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01|max:100000000',
            'reason' => 'required|string|min:5|max:500',
            'method' => ['required', 'string', ValidationRule::in(app(PaymentChannelRegistry::class)->keys())],
            'reference' => 'nullable|string|max:100',
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
            'reason.required' => 'Say why the money is being given back.',
            'reason.min' => 'Give a reason somebody can understand later.',
        ];
    }

    /**
     * Get the amount to give back, in minor units.
     */
    public function minorAmount(): int
    {
        return BrickMoney::of((string) $this->validated('amount'), config('app.currency'))
            ->getMinorAmount()
            ->toInt();
    }
}
