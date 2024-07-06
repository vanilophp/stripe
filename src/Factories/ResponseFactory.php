<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Factories;

use Illuminate\Http\Request;
use Stripe\Event;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Stripe\Messages\StripeReturnPaymentResponse;
use Vanilo\Stripe\Messages\StripeWebhookPaymentResponse;
use Vanilo\Stripe\Concerns\HasStripeConfiguration;

final class ResponseFactory
{
    use HasStripeConfiguration;

    public function create(Request $request, array $options,$secretKey): PaymentResponse
    {
        if($request->payment_intent){
            Stripe::setApiKey($this->secretKey);
            return new StripeReturnPaymentResponse(
                PaymentIntent::retrieve($request->payment_intent, [])
            );
        }

        return new StripeWebhookPaymentResponse(
            Event::constructFrom($request->all())
        );
    }
}
