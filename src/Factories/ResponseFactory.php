<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Factories;

use Illuminate\Http\Request;
use Stripe\Event;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Stripe\Messages\StripeReturnPaymentResponse;
use Vanilo\Stripe\Messages\StripeWebhookPaymentResponse;

final class ResponseFactory
{
    public function create(Request $request, array $options, string $secretKey): PaymentResponse
    {
        if ($request->payment_intent) {
            Stripe::setApiKey($secretKey);

            return new StripeReturnPaymentResponse(
                PaymentIntent::retrieve($request->payment_intent, [])
            );
        }

        return new StripeWebhookPaymentResponse(
            Event::constructFrom($request->all())
        );
    }
}
