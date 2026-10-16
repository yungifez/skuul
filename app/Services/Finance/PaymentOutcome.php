<?php

namespace App\Services\Finance;

/**
 * What a provider said about one payment attempt.
 *
 * Only a settled outcome is allowed to become a payment record. Anything else
 * leaves the books untouched, so a failed card never reduces what a family
 * owes.
 */
class PaymentOutcome
{
    /**
     * @param  int  $amount  the amount the provider took, in minor units
     * @param  array<string, mixed>  $payload  what the provider sent, kept for the audit trail
     */
    private function __construct(
        public readonly bool $settled,
        public readonly string $reference,
        public readonly int $amount,
        public readonly ?string $reason = null,
        public readonly array $payload = [],
    ) {}

    /**
     * Say the provider took the money.
     *
     * @param  array<string, mixed>  $payload
     */
    public static function settled(string $reference, int $amount, array $payload = []): self
    {
        return new self(true, $reference, $amount, null, $payload);
    }

    /**
     * Say the attempt did not produce money.
     *
     * @param  array<string, mixed>  $payload
     */
    public static function failed(string $reference, string $reason, array $payload = []): self
    {
        return new self(false, $reference, 0, $reason, $payload);
    }

    /**
     * Say the provider has not finished with the attempt.
     *
     * @param  array<string, mixed>  $payload
     */
    public static function pending(string $reference, array $payload = []): self
    {
        return new self(false, $reference, 0, 'The provider has not settled this payment yet.', $payload);
    }
}
