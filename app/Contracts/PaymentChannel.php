<?php

namespace App\Contracts;

/**
 * One way money can reach the school.
 *
 * Schools do not agree on how families pay. A village school takes cash, a
 * city school takes cards, and a school group takes card payments through a
 * provider, so the ways to pay are a list the application can grow, never a
 * fixed set written into the books.
 *
 * A new way to pay is one class and one line in
 * `App\Services\Finance\PaymentChannelRegistry`. Nothing in the ledger, the
 * invoices, or the screens changes. A channel that charges a card through a
 * provider implements `App\Contracts\OnlinePaymentGateway` as well.
 */
interface PaymentChannel
{
    /**
     * Get the name the channel is stored and chosen by.
     */
    public function key(): string;

    /**
     * Get the label to show in the interface.
     */
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
