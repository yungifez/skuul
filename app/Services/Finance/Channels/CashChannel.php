<?php

namespace App\Services\Finance\Channels;

/**
 * Money handed over at the office.
 */
class CashChannel extends OfficeChannel
{
    /**
     * Get the name the channel is stored and chosen by.
     */
    public function key(): string
    {
        return 'cash';
    }

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return 'Cash';
    }

    /**
     * Get one sentence saying when the office picks this channel.
     */
    public function description(): string
    {
        return 'Notes and coins handed over at the school office.';
    }

    /**
     * Cash stays in the cash box until somebody banks it.
     */
    public function accountPurpose(): string
    {
        return 'cash';
    }

    /**
     * Nobody quotes a reference for a handful of notes.
     */
    public function needsReference(): bool
    {
        return false;
    }
}
