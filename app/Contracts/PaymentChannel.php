<?php

namespace App\Contracts;

/**
 * Register payment channels in PaymentChannelRegistry.
 * Provider-based channels also implement OnlinePaymentGateway.
 */
interface PaymentChannel
{
    /**
     * Get the name the channel is stored and chosen by.
     */
    public function key(): string;

    public function label(): string;

    /**
     * Get one sentence saying when the office picks this channel.
     */
    public function description(): string;

    /**
     * Get the purpose of the account the money lands in.
     *
     * Cash stays in the cash box. A transfer reaches the bank.
     */
    public function accountPurpose(): string;

    /**
     * Check whether the office must record a reference for this channel.
     */
    public function needsReference(): bool;

    /**
     * Check whether the school can use this channel now.
     *
     * A provider with no keys configured is not offered, so nobody picks a
     * way to pay that cannot take the money.
     */
    public function isAvailable(): bool;
}
