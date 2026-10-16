<?php

namespace App\Services\Finance\Channels;

use App\Contracts\PaymentChannel;

/**
 * Record payments received outside the application without provider confirmation.
 */
abstract class OfficeChannel implements PaymentChannel
{
    /**
     * An office channel is always usable: it needs no keys and no provider.
     */
    public function isAvailable(): bool
    {
        return true;
    }

    /**
     * Most office channels carry a slip or a statement line to quote.
     */
    public function needsReference(): bool
    {
        return true;
    }

    /**
     * Most money that is not cash reaches the bank.
     */
    public function accountPurpose(): string
    {
        return 'bank';
    }
}
