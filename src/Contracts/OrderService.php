<?php

namespace Viviniko\Sale\Contracts;

use Viviniko\Address\Models\Address;
use Viviniko\Cart\Services\Collection;

interface OrderService
{
    /**
     * Get order.
     *
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Get order.
     *
     * @param $orderSN
     * @return mixed
     */
    public function findByOrderSN($orderSN);

    /**
     * Paginate orders.
     *
     * @param mixed $query
     *
     * @return \Common\Repository\Builder
     */
    public function search($query);

    /**
     * Create order.
     *
     * @param  Collection  $items
     * @param  Address  $address
     * @param  array  $data
     * @return  mixed
     */
    public function create(Collection $items, Address $address, array $data = []);

    public function update($orderId, array $data);

    public function changeOrderStatus($orderId, $status, $comment, $logger = null, $logLevel = 0);

    public function setOrderShippingMethod($orderId, $shippingMethodId);

    public function updateAddress($orderId, array $data);

    public function getOrderStatistics($orderId);

    public function countOrderProductQtyByLatestMonth($productId, $latestMonth = 1);
}