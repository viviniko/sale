<?php

namespace Viviniko\Sale\Repositories\OrderItem;

use Illuminate\Support\Facades\Config;
use Viviniko\Repository\EloquentRepository;

class EloquentOrderItem extends EloquentRepository implements OrderItemRepository
{
    public function __construct()
    {
        parent::__construct(Config::get('sale.order_item'));
    }
}