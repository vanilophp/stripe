<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Factories;

use Vanilo\Payment\Contracts\Payment;
use Vanilo\Stripe\Concerns\HasStripeInteraction;
use Vanilo\Stripe\Messages\StripePaymentRequest;

final class RequestFactory
{
    use HasStripeInteraction;

    public function create(Payment $payment, array $options = []): StripePaymentRequest
    {
        $result = new StripePaymentRequest();

        $result
            ->setSecretKey($this->secretKey)
            ->setPublicKey($this->publicKey)
            ->setPaymentId($payment->getPaymentId())
            ->setCurrency($payment->getCurrency())
            ->setAmount($payment->getAmount())
            ->setReturnUrl($this->returnUrl);

        if (isset($options['view'])) {
            $result->setView($options['view']);
        }

        return $result;
    }
}
