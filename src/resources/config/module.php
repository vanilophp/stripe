<?php

declare(strict_types=1);

use Vanilo\Stripe\StripePaymentGateway;

return [
    'gateway' => [
        'register' => true,
        'id' => StripePaymentGateway::DEFAULT_ID,
    ],
    'bind' => true,
    'secret_key' => env('STRIPE_SECRET_KEY', ''),
    'public_key' => env('STRIPE_PUBLIC_KEY', ''),
    'return_url' => env('STRIPE_RETURN_URL', ''),
];
