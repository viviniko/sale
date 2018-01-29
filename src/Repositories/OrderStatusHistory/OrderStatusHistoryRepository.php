<?php

namespace Viviniko\Sale\Repositories\OrderStatusHistory;

interface OrderStatusHistoryRepository
{
    public function findByOrderId($orderId);

    /**
     * Save a new entity in repository
     *
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data);
}