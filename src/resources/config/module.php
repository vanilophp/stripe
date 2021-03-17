<?php

declare(strict_types=1);

use Vanilo\Stripe\StripePaymentGateway;

return [
    'gateway' => [
        'register' => true,
        'id' => StripePaymentGateway::DEFAULT_ID
    ],
    'bind' => true,
    'xxx' => env('STRIPE_XXX'),
];
