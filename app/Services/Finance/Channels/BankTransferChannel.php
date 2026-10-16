<?php

namespace App\Services\Finance\Channels;

/**
 * Money paid straight into the school bank account.
 */
class BankTransferChannel extends OfficeChannel
{
    /**
     * Get the name the channel is stored and chosen by.
     */
    public function key(): string
    {
        return 'bank_transfer';
    }

    public function label(): string
    {
        return 'Bank transfer';
    }

    /**
     * Get one sentence saying when the office picks this channel.
     */
    public function description(): string
    {
        return 'Money that reached the school bank account.';
    }
}
