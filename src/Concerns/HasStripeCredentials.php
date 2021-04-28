<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Concerns;

trait HasStripeCredentials
{
    private string $secretKey;

    private string $publicKey;
}
