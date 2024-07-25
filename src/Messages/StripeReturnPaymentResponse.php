<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Messages;

use Konekt\Enum\Enum;
use Stripe\PaymentIntent;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Payment\Contracts\PaymentStatus;
use Vanilo\Payment\Models\PaymentStatusProxy;
use Vanilo\Stripe\Models\StripeEventType;

class StripeReturnPaymentResponse implements PaymentResponse
{
    protected PaymentIntent $intent;

    protected ?PaymentStatus $status = null;

    public function __construct(PaymentIntent $intent)
    {
        $this->intent = $intent;
    }

    public function wasSuccessful(): bool
    {
        return StripeEventType::INTENT_SUCCEEDED === $this->intent->status;
    }

    public function getMessage(): ?string
    {
        return $this->intent->description;
    }

    public function getTransactionId(): ?string
    {
        return $this->intent->id;
    }

    public function getTransactionAmount(): float
    {
        return $this->getAmountPaid() ?? 0;
    }

    public function getAmountPaid(): ?float
    {
        return (float) $this->intent->amount / 100;
    }

    public function getPaymentId(): string
    {
        return $this->intent->metadata->payment_id;
    }

    public function getStatus(): PaymentStatus
    {
        if (null === $this->status) {
            switch ($this->getNativeStatus()->value()) {
                case StripeEventType::INTENT_SUCCEEDED:
                    $this->status = PaymentStatusProxy::PAID();
                    break;
                case StripeEventType::INTENT_PROCESSING:
                    $this->status = PaymentStatusProxy::ON_HOLD();
                    break;
                default:
                    $this->status = PaymentStatusProxy::DECLINED();
            }
        }

        return $this->status;
    }

    public function getNativeStatus(): Enum
    {
        return StripeEventType::create($this->intent->status);
    }
}
