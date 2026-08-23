<?php

namespace App\Services\Finance;

use App\Models\StudentRecord;

/**
 * What a payer is about to pay, before any money has moved.
 *
 * An intent is not a payment. It carries only what a provider needs to take
 * the money and what the application needs to recognise the attempt when the
 * provider answers.
 */
class PaymentIntent
{
    /**
     * @param  int  $amount  the amount in minor units
     * @param  array<int, int>  $allocations  the invoice lines to settle, by line id and minor amount
     */
    public function __construct(
        public readonly StudentRecord $enrollment,
        public readonly int $amount,
        public readonly string $currency,
        public readonly string $reference,
        public readonly string $description,
        public readonly string $returnUrl,
        public readonly string $cancelUrl,
        public readonly array $allocations = [],
    ) {}

    /**
     * Get the amount as a decimal string, which most providers expect.
     */
    public function majorAmount(): string
    {
        return number_format($this->amount / 100, 2, '.', '');
    }
}
