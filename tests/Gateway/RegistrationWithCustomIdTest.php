<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Tests\Gateway;

use Vanilo\Payment\Contracts\PaymentGateway;
use Vanilo\Payment\PaymentGateways;
use Vanilo\Stripe\StripePaymentGateway;
use Vanilo\Stripe\Tests\TestCase;

class RegistrationWithCustomIdTest extends TestCase
{
    protected function setUp(): void
    {
        PaymentGateways::reset();
        parent::setUp();
    }

    /** @test */
    public function the_gateway_id_can_be_changed_from_within_the_configuration()
    {
        $this->assertCount(2, PaymentGateways::ids());
        $this->assertContains('yesipay', PaymentGateways::ids());
    }

    /** @test */
    public function the_gateway_can_be_instantiated()
    {
        $stripePayGateway = PaymentGateways::make('yesipay');

        $this->assertInstanceOf(PaymentGateway::class, $stripePayGateway);
        $this->assertInstanceOf(StripePaymentGateway::class, $stripePayGateway);
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        config(['vanilo.stripe.gateway.id' => 'yesipay']);
    }
}
