<?php

namespace Viviniko\Sale\Repositories\OrderItem;

use Viviniko\Repository\SimpleRepository;

class EloquentOrderItem extends SimpleRepository implements OrderItemRepository
{
    protected $modelConfigKey = 'sale.order_item';
}