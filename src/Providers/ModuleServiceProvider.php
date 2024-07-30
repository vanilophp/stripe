<?php

declare(strict_types=1);

/**
 * Contains the ModuleServiceProvider class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-03-17
 *
 */

namespace Vanilo\Stripe\Providers;

use Konekt\Concord\BaseModuleServiceProvider;
use Vanilo\Payment\PaymentGateways;
use Vanilo\Stripe\StripePaymentGateway;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    public function boot()
    {
        parent::boot();

        if ($this->config('gateway.register', true)) {
            PaymentGateways::register(
                $this->config('gateway.id', StripePaymentGateway::DEFAULT_ID),
                StripePaymentGateway::class
            );
        }

        if ($this->config('bind', true)) {
            $this->app->bind(StripePaymentGateway::class, function ($app) {
                return new StripePaymentGateway(
                    $this->config('secret_key'),
                    $this->config('public_key'),
                    $this->config('return_url'),
                    (bool) $this->config('create_customer'),
                );
            });
        }

        $this->publishes([
            $this->getBasePath() . '/' . $this->concord->getConvention()->viewsFolder() =>
            resource_path('views/vendor/stripe'),
            'vanilo-stripe'
        ]);
    }
}
