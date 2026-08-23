<?php

namespace App\Contracts;

use App\Services\Finance\PaymentIntent;
use App\Services\Finance\PaymentOutcome;
use Illuminate\Http\Request;

/**
 * A way to pay where an outside provider holds the card details.
 *
 * The school never sees a card number. The gateway sends the payer away to
 * the provider, the provider says whether the money arrived, and only then
 * does the application record a payment and post it to the books.
 *
 * Adding a provider means writing one class that implements this and naming
 * it in `App\Services\Finance\PaymentChannelRegistry`.
 */
interface OnlinePaymentGateway extends PaymentChannel
{
    /**
     * Start a payment and say where to send the payer.
     *
     * The returned address belongs to the provider.
     */
    public function checkout(PaymentIntent $intent): string;

    /**
     * Ask the provider what happened to an attempt.
     */
    public function confirm(string $reference): PaymentOutcome;

    /**
     * Read a message the provider sent to the application.
     *
     * The gateway is responsible for proving the message is genuine before
     * it reports the money as settled.
     */
    public function readCallback(Request $request): PaymentOutcome;
}
