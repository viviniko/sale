<?php

namespace Viviniko\Sale\Repositories\OrderAddress;

use Viviniko\Repository\EloquentRepository;

class EloquentOrderAddress extends EloquentRepository implements OrderAddressRepository
{
    public function __construct()
    {
        parent::__construct(Config::get('sale.order_address'));
    }
}