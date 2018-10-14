<?php

namespace Viviniko\Sale\Repositories\OrderShipping;

use Viviniko\Repository\EloquentRepository;

class EloquentOrderShipping extends EloquentRepository implements OrderShippingRepository
{
    public function __construct()
    {
        parent::__construct(Config::get('sale.order_shipping'));
    }
}