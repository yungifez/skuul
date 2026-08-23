<?php

namespace App\Services\Finance\Channels;

use App\Contracts\PaymentChannel;

/**
 * A way to pay that the office records by hand.
 *
 * The money has already arrived by the time anyone opens the application, so
 * there is nothing to confirm with anybody. Cash, cheques, and transfers read
 * off a bank statement all work this way.
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
