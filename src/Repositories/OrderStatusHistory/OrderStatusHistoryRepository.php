<?php

namespace Viviniko\Sale\Repositories\OrderStatusHistory;

use Viviniko\Repository\CrudRepository;

interface OrderStatusHistoryRepository extends CrudRepository
{
    public function findByOrderId($orderId);
}