<?php

namespace Viviniko\Sale\Repositories\OrderStatusHistory;

use Viviniko\Repository\SimpleRepository;

class EloquentOrderStatusHistory extends SimpleRepository implements OrderStatusHistoryRepository
{
    protected $modelConfigKey = 'sale.order_status_history';

    protected $fieldSearchable = ['order_id'];

    public function findByOrderId($orderId)
    {
        return $this->findBy('order_id', $orderId);
    }
}