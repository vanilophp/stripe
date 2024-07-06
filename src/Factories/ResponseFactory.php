<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Factories;

use Illuminate\Http\Request;
use Stripe\Event;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Stripe\Messages\StripePaymentResponse;

final class ResponseFactory
{
    public function create(Request $request, array $options): PaymentResponse
    {
        if($request->payment_intent){
            return new StripeReturnPaymentResponse(
                PaymentIntent::retrieve($request->payment_intent, [])
            );
        }

        return new StripeWebhookPaymentResponse(
            Event::constructFrom($request->all())
        );
    }
}
