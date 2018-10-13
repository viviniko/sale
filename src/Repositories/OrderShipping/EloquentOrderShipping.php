<?php

namespace Viviniko\Sale\Repositories\OrderShipping;

use Viviniko\Repository\EloquentRepository;

class EloquentOrderShipping extends EloquentRepository implements OrderShippingRepository
{
    public function __construct()
    {
        parent::__construct('sale.order_shipping');
    }
}