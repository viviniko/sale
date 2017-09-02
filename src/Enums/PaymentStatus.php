<?php

namespace Viviniko\Sale\Enums;

class PaymentStatus
{
    const COMPLETED = 'Completed';
    const DENIED = 'Denied';
    const CANCELED_REVERSAL = 'Canceled-Reversal';
    const EXPIRED = 'Expired';
    const FAILED = 'Failed';
    const IN_PROGRESS = 'In-Progress';
    const PARTIALLY_REFUNDED = 'Partially-Refunded';
    const PENDING = 'Pending';
    const REFUNDED = 'Refunded';
    const PROCESSED = 'Processed';
    const VOIDED = 'Voided';
    const COMPLETED_FUNDS_HELD = 'Completed-Funds-Held';
    const NONE = 'None';
    const AUTHORIZING = 'Authorizing';
    const ERROR = 'Error';

    public static function values()
    {
        return [
            static::COMPLETED => static::COMPLETED,
            static::DENIED => static::DENIED,
            static::CANCELED_REVERSAL => static::CANCELED_REVERSAL,
            static::EXPIRED => static::EXPIRED,
            static::FAILED => static::FAILED,
            static::IN_PROGRESS => static::IN_PROGRESS,
            static::PARTIALLY_REFUNDED => static::PARTIALLY_REFUNDED,
            static::PENDING => static::PENDING,
            static::REFUNDED => static::REFUNDED,
            static::PROCESSED => static::PROCESSED,
            static::VOIDED => static::VOIDED,
            static::COMPLETED_FUNDS_HELD => static::COMPLETED_FUNDS_HELD,
            static::NONE => static::NONE,
            static::ERROR => static::ERROR,
            static::AUTHORIZING => static::AUTHORIZING,
        ];
    }
}