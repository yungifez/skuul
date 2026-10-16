<?php

namespace App\Contracts;

use App\Services\Finance\PaymentIntent;
use App\Services\Finance\PaymentOutcome;
use Illuminate\Http\Request;

/**
 * The provider collects card details. Record payments only after it confirms settlement.
 * Register gateways in PaymentChannelRegistry.
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
