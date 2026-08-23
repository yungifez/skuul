<?php

namespace App\Services\Finance\Channels;

use App\Contracts\OnlinePaymentGateway;
use App\Exceptions\InvalidValueException;
use App\Services\Finance\PaymentIntent;
use App\Services\Finance\PaymentOutcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Card payments taken through Stripe.
 *
 * The school never handles a card number. Stripe collects it, and the
 * application records a payment only after Stripe says the money settled.
 *
 * This class is the worked example of `App\Contracts\OnlinePaymentGateway`.
 * Another provider is another class beside this one.
 */
class StripeChannel implements OnlinePaymentGateway
{
    /**
     * How long Stripe's signature stays acceptable, in seconds.
     */
    private const SIGNATURE_TOLERANCE = 300;

    /**
     * Get the name the channel is stored and chosen by.
     */
    public function key(): string
    {
        return 'stripe';
    }

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return 'Card payment (Stripe)';
    }

    /**
     * Get one sentence saying when the office picks this channel.
     */
    public function description(): string
    {
        return 'A card the family paid online. Stripe takes the money and tells the school.';
    }

    /**
     * Card money reaches the bank, not the cash box.
     */
    public function accountPurpose(): string
    {
        return 'bank';
    }

    /**
     * Stripe names every payment, and the school keeps that name.
     */
    public function needsReference(): bool
    {
        return true;
    }

    /**
     * Offer this channel only once the school has set its Stripe keys.
     */
    public function isAvailable(): bool
    {
        return is_string(config('services.stripe.secret')) && config('services.stripe.secret') !== '';
    }

    /**
     * Open a Stripe checkout and say where to send the payer.
     *
     * @throws InvalidValueException when Stripe refuses to start the payment
     */
    public function checkout(PaymentIntent $intent): string
    {
        $response = Http::withToken($this->secret())
            ->asForm()
            ->post($this->endpoint().'/checkout/sessions', [
                'mode' => 'payment',
                'success_url' => $intent->returnUrl,
                'cancel_url' => $intent->cancelUrl,
                'client_reference_id' => $intent->reference,
                'line_items[0][quantity]' => 1,
                'line_items[0][price_data][currency]' => strtolower($intent->currency),
                'line_items[0][price_data][unit_amount]' => $intent->amount,
                'line_items[0][price_data][product_data][name]' => $intent->description,
                'metadata[reference]' => $intent->reference,
                'metadata[student_record_id]' => $intent->enrollment->id,
            ]);

        if ($response->failed() || !is_string($response->json('url'))) {
            throw new InvalidValueException('Stripe could not start this payment. Try again, or take the money another way.');
        }

        return $response->json('url');
    }

    /**
     * Ask Stripe what happened to a checkout.
     */
    public function confirm(string $reference): PaymentOutcome
    {
        $response = Http::withToken($this->secret())
            ->get($this->endpoint()."/checkout/sessions/$reference");

        if ($response->failed()) {
            return PaymentOutcome::failed($reference, 'Stripe does not know this payment.');
        }

        return $this->read($reference, $response->json());
    }

    /**
     * Read a message Stripe sent, once its signature proves who sent it.
     */
    public function readCallback(Request $request): PaymentOutcome
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature', '');

        if (!$this->signatureIsGenuine($payload, $signature)) {
            return PaymentOutcome::failed('', 'This message did not come from Stripe.');
        }

        /** @var array<string, mixed> $event */
        $event = json_decode($payload, true) ?: [];
        $session = $event['data']['object'] ?? [];
        $reference = is_array($session) ? (string) ($session['id'] ?? '') : '';

        if (($event['type'] ?? '') !== 'checkout.session.completed') {
            return PaymentOutcome::pending($reference, is_array($session) ? $session : []);
        }

        return $this->read($reference, is_array($session) ? $session : []);
    }

    /**
     * Turn one Stripe checkout into an answer the application understands.
     *
     * @param  array<string, mixed>|null  $session
     */
    private function read(string $reference, ?array $session): PaymentOutcome
    {
        $session ??= [];

        if (($session['payment_status'] ?? null) !== 'paid') {
            return PaymentOutcome::pending($reference, $session);
        }

        return PaymentOutcome::settled($reference, (int) ($session['amount_total'] ?? 0), $session);
    }

    /**
     * Check the signature Stripe sends beside every message.
     *
     * Stripe signs the timestamp and the body together. A message that is old
     * or signed with another key is refused, so nobody can claim a payment
     * arrived by posting to the address themselves.
     */
    private function signatureIsGenuine(string $payload, string $header): bool
    {
        $secret = config('services.stripe.webhook_secret');

        if (!is_string($secret) || $secret === '' || $header === '') {
            return false;
        }

        $parts = [];

        foreach (explode(',', $header) as $piece) {
            [$name, $value] = array_pad(explode('=', trim($piece), 2), 2, '');
            $parts[$name][] = $value;
        }

        $timestamp = $parts['t'][0] ?? null;

        if ($timestamp === null || abs(time() - (int) $timestamp) > self::SIGNATURE_TOLERANCE) {
            return false;
        }

        $expected = hash_hmac('sha256', "$timestamp.$payload", $secret);

        foreach ($parts['v1'] ?? [] as $candidate) {
            if (hash_equals($expected, $candidate)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the key the school signed up with.
     */
    private function secret(): string
    {
        $secret = config('services.stripe.secret');

        if (!is_string($secret) || $secret === '') {
            throw new InvalidValueException('This school has not set up card payments yet.');
        }

        return $secret;
    }

    /**
     * Get the address Stripe answers on.
     */
    private function endpoint(): string
    {
        $endpoint = config('services.stripe.endpoint');

        return is_string($endpoint) && $endpoint !== '' ? rtrim($endpoint, '/') : 'https://api.stripe.com/v1';
    }
}
