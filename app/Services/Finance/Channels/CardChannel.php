<?php

namespace App\Services\Finance\Channels;

/**
 * A card paid on the school card machine.
 */
class CardChannel extends OfficeChannel
{
    /**
     * Get the name the channel is stored and chosen by.
     */
    public function key(): string
    {
        return 'card';
    }

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return 'Card machine';
    }

    /**
     * Get one sentence saying when the office picks this channel.
     */
    public function description(): string
    {
        return 'A card paid on the machine at the office.';
    }
}
