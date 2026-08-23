<?php

namespace App\Services\Finance\Channels;

/**
 * A transfer from a mobile money wallet.
 */
class MobileMoneyChannel extends OfficeChannel
{
    /**
     * Get the name the channel is stored and chosen by.
     */
    public function key(): string
    {
        return 'mobile_money';
    }

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return 'Mobile money';
    }

    /**
     * Get one sentence saying when the office picks this channel.
     */
    public function description(): string
    {
        return 'A transfer from a mobile money wallet.';
    }
}
