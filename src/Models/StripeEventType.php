<?php

declare(strict_types=1);

namespace Vanilo\Stripe\Models;

use Konekt\Enum\Enum;

class StripeEventType extends Enum
{
    public const __DEFAULT = self::CHARGE_FAILED;

    public const CHARGE_CAPTURED = 'charge.captured';
    public const CHARGE_EXPIRED = 'charge.expired';
    public const CHARGE_FAILED = 'charge.failed';
    public const CHARGE_PENDING = 'charge.pending';
    public const CHARGE_REFUNDED = 'charge.refunded';
    public const CHARGE_SUCCEEDED = 'charge.succeeded';
    public const CHARGE_UPDATED = 'charge.updated';

    protected static $unknownValuesFallbackToDefault = true;
}
