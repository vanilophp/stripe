<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Concerns;

trait HasStripeInteraction
{
    use HasStripeConfiguration;

    public function __construct(
        string $secretKey,
        string $publicKey,
        ?string $returnUrl = null,
        ?bool $createCustomer = false,
    ) {
        $this->secretKey = $secretKey;
        $this->publicKey = $publicKey;
        $this->returnUrl = $returnUrl;
        $this->createCustomer = $createCustomer;
    }
}
