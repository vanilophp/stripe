# Configuration

## Dotenv Values

The following `.env` parameters can be set in order to work with this package.

```dotenv
STRIPE_SECRET_KEY="" # Mandatory
STRIPE_PUBLIC_KEY="" # Mandatory
STRIPE_RETURN_URL="" # Optional, leave this unset unless you really want to set a different return_url for stripe
STRIPE_CREATE_CUSTOMER=0# Set to 1 or true if you want a Stripe customer assigned/created along with the payment intent
```

The return URL should be an endpoint in your final application that handles the requests upon payment state change.

An example implementation:

```php
use Illuminate\Http\Request;
use Vanilo\Payment\Models\Payment;
use Vanilo\Payment\PaymentGateways;
use Vanilo\Payment\Processing\PaymentResponseHandler;

class StripeController
{
    public function return(Request $request)
    {
        $response = PaymentGateways::make('stripe')->processPaymentResponse($request);
        $payment = Payment::findByPaymentId($response->getPaymentId());

        if (null === $payment) {
            abort(404);
        }

        try {
            $handler = new PaymentResponseHandler($payment, $response);
            $handler->writeResponseToHistory();
            $handler->updatePayment();
            $handler->fireEvents();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error at processing: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Payment processed successfully']);
    }
}
```

## Registration with Payments Module

The module will automatically register the payment gateway with the Vanilo Payments registry by
default. Having that, you can get a gateway instance directly from the Payment registry:

```php
$stripeGateway = \Vanilo\Payment\PaymentGateways::make('stripe');
```

### Registering With Another Name

If you'd like to use another name in the payment registry, it can be done in the module config:

```php
//config/concord.php
return [
    'modules' => [
        //...
        Vanilo\Stripe\Providers\ModuleServiceProvider::class => [
            'gateway' => [
                'id' => 'stripe-gateway'
            ]
        ]
        //...
    ]
];
```

After this you can obtain a gateway instance with the configured name:

```php
\Vanilo\Payment\PaymentGateways::make('stripe-gateway');
```

### Prevent from Auto-registration

If you don't want it to be registered automatically, you can prevent it by changing the module
configuration:

```php
//config/concord.php
return [
    'modules' => [
        //...
        Vanilo\Stripe\Providers\ModuleServiceProvider::class => [
            'gateway' => [
                'register' => false
            ]
        ]
        //...
    ]
];
```

### Manual Registration

If you disable registration and want to register the gateway manually you can do it by using the
Vanilo Payment module's payment gateway registry:

```php
use Vanilo\Stripe\StripePaymentGateway;
use Vanilo\Payment\PaymentGateways;

PaymentGateways::register('stripe-or-whatever-name-you-want', StripePaymentGateway::class);
```

## Binding With The Laravel Container

By default `StripePaymentGateway::class` gets bound to the Laravel DI container, so that you can
obtain a properly autoconfigured instance. Typically, you don't get the instance directly from the
Laravel container (ie. `app()->make(StripePaymentGateway::class)`) but from the Vanilo Payment
Gateway registry:

```php
$instance = \Vanilo\Payment\PaymentGateways::make('stripe');
```

The default DI binding happens so that all the configuration parameters are read from the config values
mentioned above. This will work out of the box and will be sufficient for most of the applications.

### Manual Binding

It is possible to prevent the automatic binding and thus configure the Gateway in a custom way in
the module config:

```php
//config/concord.php
return [
    'modules' => [
        Vanilo\Stripe\Providers\ModuleServiceProvider::class => [
            'bind' => false,
```

This can be useful if the Gateway configuration can't be set in the env file, for example when:

- The credentials can be **configured in an Admin interface** instead of `.env`
- Your app has **multiple payment methods** that use Stripe with **different credentials**
- There is a **multi-tenant application**, where each tenant has their own credentials

Setting `vanilo.stripe.bind` to `false` will cause that the class doesn't get bound with the
Laravel DI container automatically. Therefore, you need to do this yourself in your application,
typically in the `AppServiceProvider::boot()` method:

```php
$this->app->bind(StripePaymentGateway::class, function ($app) {
    return new StripePaymentGateway(
        config('vanilo.stripe.secret_key'),  // You can use any source
        config('vanilo.stripe.public_key'),  // other than config()
        config('vanilo.stripe.return_url'),  // for passing args
        (bool) config(vanilo.stripe.'create_customer'),
    );
});
```

---

**Next**: [Workflow &raquo;](workflow.md)
