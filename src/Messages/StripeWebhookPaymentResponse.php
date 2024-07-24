<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Messages;

use Konekt\Enum\Enum;
use Stripe\Charge;
use Stripe\Event;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Payment\Contracts\PaymentStatus;
use Vanilo\Payment\Models\PaymentStatusProxy;
use Vanilo\Stripe\Models\StripeEventType;

class StripeWebhookPaymentResponse implements PaymentResponse
{
    protected string $eventType;

    protected Charge $charge;

    protected ?PaymentStatus $status = null;

    public function __construct(Event $event)
    {
        $this->eventType = $event->type;
        $this->charge = $event->data->object;
    }

    public function wasSuccessful(): bool
    {
        return Charge::STATUS_SUCCEEDED === $this->charge->status;
    }

    public function getMessage(): ?string
    {
        if ($this->wasSuccessful() && $this->charge->outcome) {
            return $this->charge->outcome->seller_message;
        }

        return $this->charge->failure_message;
    }

    public function getTransactionId(): ?string
    {
        return $this->charge->id;
    }

    public function getTransactionAmount(): float
    {
        return $this->getAmountPaid() ?? 0;
    }

    public function getAmountPaid(): ?float
    {
        if ($this->getNativeStatus()->isChargeRefunded()) {
            return (float) ($this->charge->amount - $this->charge->amount_refunded) / 100;
        }

        return (float) $this->charge->amount_captured / 100;
    }

    public function getPaymentId(): string
    {
        return $this->charge->metadata->payment_id ?? "-1";
    }

    public function getStatus(): PaymentStatus
    {
        if (null === $this->status) {
            switch ($this->getNativeStatus()->value()) {
                case StripeEventType::CHARGE_SUCCEEDED:
                    $this->status = PaymentStatusProxy::PAID();
                    break;
                case StripeEventType::CHARGE_EXPIRED:
                    $this->status = PaymentStatusProxy::TIMEOUT();
                    break;
                case StripeEventType::CHARGE_PENDING:
                    $this->status = PaymentStatusProxy::ON_HOLD();
                    break;
                case StripeEventType::CHARGE_REFUNDED:
                    $this->status = $this->charge->refunded ? PaymentStatusProxy::REFUNDED() : PaymentStatusProxy::PARTIALLY_REFUNDED();
                    break;
                default:
                    $this->status = PaymentStatusProxy::DECLINED();
            }
        }

        return $this->status;
    }

    public function getNativeStatus(): Enum
    {
        return StripeEventType::create($this->eventType);
    }
}
