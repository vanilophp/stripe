<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Concerns;

trait HasStripeConfiguration
{
    use HasStripeCredentials;

    private ?string $returnUrl = null;

    private ?bool $createCustomer = false;
}
