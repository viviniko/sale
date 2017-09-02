<?php

namespace Viviniko\Sale\Repositories\OrderAddress;

use Viviniko\Repository\SimpleRepository;

class EloquentOrderAddress extends SimpleRepository implements OrderAddressRepository
{
    protected $modelConfigKey = 'sale.order_address';
}