<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Messages;

use Stripe\Customer;
use Stripe\Stripe;
use Vanilo\Contracts\Billpayer;
use Vanilo\Stripe\Concerns\HasStripeConfiguration;

class StripeCustomerRequest
{
    use HasStripeConfiguration;

    public function resolveCustomerId(Billpayer $billpayer): ?string
    {
        Stripe::setApiKey($this->secretKey);

        $customer = Customer::search(['query' => "email:'{$billpayer->getEmail()}'"])->first();

        if (!$customer) {
            $customer = Customer::create([
                'email' => $billpayer->getEmail(),
                'name' => $billpayer->getName(),
                'phone' => $billpayer->getPhone(),
                'address' => [
                    'city' => $billpayer->getBillingAddress()->getCity(),
                    'line1' => $billpayer->getBillingAddress()->getAddress(),
                    'line2' => $billpayer->getBillingAddress()->getAddress2(),
                    'postal_code' => $billpayer->getBillingAddress()->getPostalCode(),
                    'country' => $billpayer->getBillingAddress()->getCountryCode(),
                    'state' => $billpayer->getBillingAddress()->getProvinceCode(),
                ],
            ]);
        }

        return $customer->id;
    }

    public function setSecretKey(string $secretKey): self
    {
        $this->secretKey = $secretKey;

        return $this;
    }
}
