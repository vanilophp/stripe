<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Messages;

use Konekt\Enum\Enum;
use Stripe\PaymentIntent;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Payment\Contracts\PaymentStatus;

class StripePaymentResponse implements PaymentResponse
{
    protected PaymentIntent $intent;

    public function __construct(PaymentIntent $intent)
    {
        $this->intent = $intent;
    }

    public function wasSuccessful(): bool
    {
        return $this->intent->status === PaymentIntent::STATUS_SUCCEEDED;
    }

    public function getMessage(): ?string
    {
        return $this->intent->description;
    }

    public function getTransactionId(): ?string
    {
        return $this->intent->id;
    }

    public function getAmountPaid(): ?float
    {
        return (float)$this->intent->amount_received;
    }

    public function getPaymentId(): string
    {
        return $this->intent->metadata->payment_id;
    }


    public function getStatus(): PaymentStatus
    {

    }

    public function getNativeStatus(): Enum
    {
    }
}
