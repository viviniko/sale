<?php

namespace Viviniko\Sale\Repositories\Order;

use Viviniko\Repository\SimpleRepository;

class EloquentOrder extends SimpleRepository implements OrderRepository
{
    protected $modelConfigKey = 'sale.order';

    protected $fieldSearchable = [
        'id',
        'order_sn' => 'like',
        'customer_id',
        'status',
        'created_at' => 'betweenDate',
    ];
}