<?php

namespace App\Services\Finance\Channels;

/**
 * A cheque handed to the office and banked.
 */
class ChequeChannel extends OfficeChannel
{
    /**
     * Get the name the channel is stored and chosen by.
     */
    public function key(): string
    {
        return 'cheque';
    }

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return 'Cheque';
    }

    /**
     * Get one sentence saying when the office picks this channel.
     */
    public function description(): string
    {
        return 'A cheque the school has banked.';
    }
}
