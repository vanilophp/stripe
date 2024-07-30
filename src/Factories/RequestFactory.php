<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Factories;

use Vanilo\Payment\Contracts\Payment;
use Vanilo\Stripe\Concerns\HasStripeInteraction;
use Vanilo\Stripe\Messages\StripeCustomerRequest;
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
            ->setDescription("Order number: {$payment->getPayable()->getPayableId()}")
            ->setPaymentId($payment->getPaymentId())
            ->setCurrency($payment->getCurrency())
            ->setAmount($payment->getAmount())
            ->setReturnUrl($this->returnUrl);

        if ($this->createCustomer && $billpayer = $payment->getPayable()->getBillpayer()) {
            try {
                $customerId = (new StripeCustomerRequest())
                    ->setSecretKey($this->secretKey)
                    ->resolveCustomerId($billpayer);

                $result->setCustomerId($customerId);
            } catch (\Exception $e) {
                //If customer can't be created still the payment should be fine
            }
        }

        if (isset($options['view'])) {
            $result->setView($options['view']);
        }

        return $result;
    }
}
