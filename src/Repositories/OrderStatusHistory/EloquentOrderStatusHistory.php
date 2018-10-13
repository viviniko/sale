<?php

namespace Viviniko\Sale\Repositories\OrderStatusHistory;

use Viviniko\Repository\SimpleRepository;

class EloquentOrderStatusHistory extends SimpleRepository implements OrderStatusHistoryRepository
{

    public function __construct()
    {
        parent::__construct('sale.order_status_history');
    }

    public function findByOrderId($orderId)
    {
        return $this->findBy('order_id', $orderId);
    }
}