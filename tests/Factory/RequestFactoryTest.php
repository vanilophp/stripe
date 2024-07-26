<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Tests\Factory;

use Vanilo\Payment\Factories\PaymentFactory;
use Vanilo\Payment\Models\PaymentMethod;
use Vanilo\Stripe\Factories\RequestFactory;
use Vanilo\Stripe\Messages\StripePaymentRequest;
use Vanilo\Stripe\StripePaymentGateway;
use Vanilo\Stripe\Tests\Dummies\Order;
use Vanilo\Stripe\Tests\TestCase;

class RequestFactoryTest extends TestCase
{
    /** @test */
    public function it_creates_a_request_object()
    {
        $factory = new RequestFactory('secret', 'pkey', url('/'));
        $method = PaymentMethod::create([
            'gateway' => StripePaymentGateway::getName(),
            'name' => 'Stripe',
        ]);

        $order = Order::create(['currency' => 'USD', 'amount' => 13.99]);

        $payment = PaymentFactory::createFromPayable($order, $method);

        $this->assertInstanceOf(
            StripePaymentRequest::class,
            $factory->create($payment)
        );
    }
}
