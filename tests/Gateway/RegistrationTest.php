<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Tests\Gateway;

use Vanilo\Payment\Contracts\PaymentGateway;
use Vanilo\Payment\PaymentGateways;
use Vanilo\Stripe\StripePaymentGateway;
use Vanilo\Stripe\Tests\TestCase;

class RegistrationTest extends TestCase
{
    /** @test */
    public function the_gateway_is_registered_out_of_the_box_with_defaults()
    {
        $this->assertCount(2, PaymentGateways::ids());
        $this->assertContains(StripePaymentGateway::DEFAULT_ID, PaymentGateways::ids());
    }

    /** @test */
    public function the_gateway_can_be_instantiated()
    {
        $stripePayGateway = PaymentGateways::make('stripe');

        $this->assertInstanceOf(PaymentGateway::class, $stripePayGateway);
        $this->assertInstanceOf(StripePaymentGateway::class, $stripePayGateway);
    }

    /** @test */
    public function the_gateway_has_a_logo()
    {
        $this->assertNotEmpty(StripePaymentGateway::svgIcon());
    }
}
