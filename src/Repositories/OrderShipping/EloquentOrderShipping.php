<?php

namespace Viviniko\Sale\Repositories\OrderShipping;

use Viviniko\Repository\SimpleRepository;

class EloquentOrderShipping extends SimpleRepository implements OrderShippingRepository
{
    protected $modelConfigKey = 'sale.order_shipping';
}