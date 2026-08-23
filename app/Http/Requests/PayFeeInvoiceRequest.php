<?php

namespace App\Http\Requests;

use App\Services\Finance\PaymentChannelRegistry;
use Brick\Money\Money as BrickMoney;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class PayFeeInvoiceRequest extends FormRequest
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
            'method' => ['required', 'string', ValidationRule::in(app(PaymentChannelRegistry::class)->keys())],
            'reference' => 'nullable|string|max:100',
            'note' => 'nullable|string|max:1000',
            'received_on' => 'nullable|date|before_or_equal:today',
            'spread' => 'nullable|in:oldest_first,by_line',
            'lines' => 'nullable|array',
            'lines.*' => 'nullable|numeric|min:0',
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
            'amount.required' => 'Say how much money arrived.',
            'method.required' => 'Say how the money reached the school.',
            'method.in' => 'This school does not take money that way.',
        ];
    }

    /**
     * Get the amount that arrived, in minor units.
     */
    public function minorAmount(): int
    {
        return BrickMoney::of($this->validated('amount'), config('app.currency'))
            ->getMinorAmount()
            ->toInt();
    }

    /**
     * Get the amount to write against each invoice line, in minor units.
     *
     * Nothing is returned when the office asked the application to clear the
     * oldest bills by itself.
     *
     * @return array<int, int>|null
     */
    public function allocationPlan(): ?array
    {
        if ($this->validated('spread') !== 'by_line') {
            return null;
        }

        $plan = [];

        foreach ((array) $this->validated('lines', []) as $lineId => $share) {
            if ($share === null || $share === '' || (float) $share <= 0) {
                continue;
            }

            $plan[(int) $lineId] = BrickMoney::of($share, config('app.currency'))->getMinorAmount()->toInt();
        }

        return $plan;
    }
}
