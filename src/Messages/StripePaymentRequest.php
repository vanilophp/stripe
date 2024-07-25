<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Messages;

use Illuminate\Support\Facades\View;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Vanilo\Payment\Contracts\PaymentRequest;
use Vanilo\Stripe\Concerns\HasStripeConfiguration;

class StripePaymentRequest implements PaymentRequest
{
    use HasStripeConfiguration;

    private string $paymentId;

    private string $currency;

    private float $amount;

    private ?string $returnUrl = null;

    private string $view = 'stripe::_request';

    public function getHtmlSnippet(array $options = []): ?string
    {
        Stripe::setApiKey($this->secretKey);
        $paymentIntent = PaymentIntent::create([
            'amount' => $this->amount * 100,
            'currency' => $this->currency,
            'metadata' => [
                'payment_id' => $this->paymentId
            ]
        ]);

        return View::make(
            $this->view,
            [
                'publicKey' => $this->publicKey,
                'intentSecret' => $paymentIntent->client_secret,
                'returnUrl' => $this->returnUrl
            ]
        )->render();
    }

    public function getPublicKey()
    {
        return $this->publicKey;
    }
    public function willRedirect(): bool
    {
        return true;
    }

    public function setSecretKey(string $secretKey): self
    {
        $this->secretKey = $secretKey;

        return $this;
    }

    public function setPublicKey(string $publicKey): self
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    public function setPaymentId(string $paymentId): self
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getRemoteId(): ?string
    {
        return null;
    }

    public function setView(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    public function setReturnUrl(string $returnUrl): self
    {
        $this->returnUrl = $returnUrl;

        return $this;
    }
}
