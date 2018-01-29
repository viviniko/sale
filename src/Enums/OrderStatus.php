<?php

namespace Viviniko\Sale\Enums;

class OrderStatus
{
    const UNPAID = 'Unpaid';
    const DECIEVE = 'Decieve';
    const DENIED = 'Denied';
    const PAID_PROCESSING = 'Paid Processing';
    const ISSUE = 'Issue';
    const COMPLETED = 'Completed';
    const PAID = 'Paid';
    const REFUNDED = 'Refunded';
    const EMAILED = 'E-mailed';
    const CANCELED_REVERSAL = 'Canceled Reversal';
    const PART_REFUNDED = 'Part Refunded';
    const SHIPPED = 'Shipped';
    const CANCELED = 'Canceled';

    public static function values(){
        return [
            static::UNPAID => static::UNPAID,
            static::PAID => static::PAID,
            static::PAID_PROCESSING => static::PAID_PROCESSING,
            static::COMPLETED => static::COMPLETED,
            static::PART_REFUNDED => static::PART_REFUNDED,
            static::REFUNDED => static::REFUNDED,
            static::SHIPPED => static::SHIPPED,
            static::CANCELED => static::CANCELED,
            static::DECIEVE => static::DECIEVE,
            static::DENIED => static::DENIED,
            static::ISSUE => static::ISSUE,
            static::EMAILED => static::EMAILED,
            static::CANCELED_REVERSAL => static::CANCELED_REVERSAL,
        ];
    }
}

