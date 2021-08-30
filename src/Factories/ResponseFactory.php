<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Factories;

use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Stripe\Concerns\HasStripeInteraction;
use Vanilo\Stripe\Messages\StripePaymentResponse;

final class ResponseFactory
{
    use HasStripeInteraction;

    public function create(Request $request, array $options): PaymentResponse
    {
        $request->validate([
            'paymentIntentId' => 'required'
        ]);

        Stripe::setApiKey($this->secretKey);

        return new StripePaymentResponse(
            PaymentIntent::retrieve($request->get('paymentIntentId'))
        );
    }
}
