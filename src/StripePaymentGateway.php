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
use Vanilo\Payment\Contracts\TransactionHandler;
use Vanilo\Stripe\Concerns\HasStripeInteraction;
use Vanilo\Stripe\Factories\RequestFactory;
use Vanilo\Stripe\Factories\ResponseFactory;

class StripePaymentGateway implements PaymentGateway
{
    use HasStripeInteraction;

    public const DEFAULT_ID = 'stripe';

    private static ?string $svg = null;

    private ?RequestFactory $requestFactory = null;

    private ?ResponseFactory $responseFactory = null;

    public static function getName(): string
    {
        return 'Stripe';
    }

    public function createPaymentRequest(Payment $payment, Address $shippingAddress = null, array $options = []): PaymentRequest
    {
        if (null === $this->requestFactory) {
            $this->requestFactory = new RequestFactory(
                $this->secretKey,
                $this->publicKey,
                $this->returnUrl,
                $this->createCustomer
            );
        }

        return $this->requestFactory->create($payment, $options);
    }

    public function processPaymentResponse(Request $request, array $options = []): PaymentResponse
    {
        if (null === $this->responseFactory) {
            $this->responseFactory = new ResponseFactory();
        }

        return $this->responseFactory->create($request, $options, $this->secretKey);
    }

    public static function svgIcon(): string
    {
        return self::$svg ??= file_get_contents(__DIR__ . '/resources/logo.svg');
    }

    public function transactionHandler(): ?TransactionHandler
    {
        return null;
    }

    public function isOffline(): bool
    {
        return false;
    }
}
