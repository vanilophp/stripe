<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Factories;

use Illuminate\Http\Request;
use Stripe\Charge;
use Stripe\Event;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Stripe\Concerns\HasStripeInteraction;
use Vanilo\Stripe\Messages\StripePaymentResponse;

final class ResponseFactory
{
    public function create(Request $request, array $options): PaymentResponse
    {
        return new StripePaymentResponse(
            Event::constructFrom($request->all())
        );
    }
}
