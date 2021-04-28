<?php

declare(strict_types=1);

/**
 * Contains the StripePaymentGateway class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-03-17
 *
 */

namespace Vanilo\Stripe;

use Illuminate\Http\Request;
use Vanilo\Contracts\Address;
use Vanilo\Payment\Contracts\Payment;
use Vanilo\Payment\Contracts\PaymentGateway;
use Vanilo\Payment\Contracts\PaymentRequest;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Stripe\Concerns\HasStripeInteraction;
use Vanilo\Stripe\Factories\RequestFactory;

class StripePaymentGateway implements PaymentGateway
{
    use HasStripeInteraction;

    public const DEFAULT_ID = 'stripe';

    private ?RequestFactory $requestFactory = null;

    public static function getName(): string
    {
        return 'Stripe';
    }

    public function createPaymentRequest(Payment $payment, Address $shippingAddress = null, array $options = []): PaymentRequest
    {
        if (null === $this->requestFactory) {
            $this->requestFactory = new RequestFactory(
                $this->secretKey,
                $this->publicKey
            );
        }

        return $this->requestFactory->create($payment, $options);
    }

    public function processPaymentResponse(Request $request, array $options = []): PaymentResponse
    {
        // @todo implement
    }

    public function isOffline(): bool
    {
        return false;
    }
}
