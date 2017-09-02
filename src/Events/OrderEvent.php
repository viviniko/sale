<?php

namespace Viviniko\Sale\Events;

use Viviniko\Sale\Models\Order;

abstract class OrderEvent
{
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}