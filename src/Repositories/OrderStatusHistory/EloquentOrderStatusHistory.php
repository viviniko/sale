<?php

namespace Viviniko\Sale\Repositories\OrderStatusHistory;

use Illuminate\Support\Facades\Config;
use Viviniko\Repository\EloquentRepository;

class EloquentOrderStatusHistory extends EloquentRepository implements OrderStatusHistoryRepository
{

    public function __construct()
    {
        parent::__construct(Config::get('sale.order_status_history'));
    }

    public function findByOrderId($orderId)
    {
        return $this->findBy('order_id', $orderId);
    }
}