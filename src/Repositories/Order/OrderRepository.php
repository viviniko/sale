<?php

namespace Viviniko\Sale\Repositories\Order;

interface OrderRepository
{
    public function exists($column, $value = null);
}