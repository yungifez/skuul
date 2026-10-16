<?php

namespace App\Services\Finance;

use App\Contracts\PaymentChannel;
use App\Exceptions\InvalidValueException;
use App\Services\Finance\Channels\BankTransferChannel;
use App\Services\Finance\Channels\CardChannel;
use App\Services\Finance\Channels\CashChannel;
use App\Services\Finance\Channels\ChequeChannel;
use App\Services\Finance\Channels\MobileMoneyChannel;
use App\Services\Finance\Channels\StripeChannel;

/**
 * The ways this application can take money.
 *
 * Add a channel by writing the class and naming it here. The payment records,
 * the ledger, and the screens read the list, so none of them change.
 */
class PaymentChannelRegistry
{
    /**
     * The channels, in the order the screens offer them.
     *
     * @var array<int, class-string<PaymentChannel>>
     */
    private const CHANNELS = [
        CashChannel::class,
        BankTransferChannel::class,
        ChequeChannel::class,
        CardChannel::class,
        MobileMoneyChannel::class,
        StripeChannel::class,
    ];

    /**
     * Get the channel with the given name.
     *
     * @throws InvalidValueException when no channel has that name
     */
    public function get(string $key): PaymentChannel
    {
        foreach (self::CHANNELS as $class) {
            /** @var PaymentChannel $channel */
            $channel = app($class);

            if ($channel->key() === $key) {
                return $channel;
            }
        }

        throw new InvalidValueException("There is no way to pay called $key.");
    }

    /**
     * Check whether a channel goes by this name.
     */
    public function has(string $key): bool
    {
        return in_array($key, $this->keys(), true);
    }

    /**
     * Get every channel a school can use now, keyed by name.
     *
     * A provider with no keys set is left out, so nobody picks a way to pay
     * that cannot take the money.
     *
     * @return array<string, PaymentChannel>
     */
    public function all(): array
    {
        $channels = [];

        foreach (self::CHANNELS as $class) {
            /** @var PaymentChannel $channel */
            $channel = app($class);

            if ($channel->isAvailable()) {
                $channels[$channel->key()] = $channel;
            }
        }

        return $channels;
    }

    /**
     * Get the names a form may send.
     *
     * @return array<int, string>
     */
    public function keys(): array
    {
        return array_keys($this->all());
    }

    /**
     * Get the names and labels, for a select box.
     *
     * @return array<string, string>
     */
    public function options(): array
    {
        return array_map(fn (PaymentChannel $channel): string => $channel->label(), $this->all());
    }
}
