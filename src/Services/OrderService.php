<?php

namespace Viviniko\Sale\Services;

use Viviniko\Address\Models\Address;
use Viviniko\Cart\Collection;

interface OrderService
{

    /**
     * Create order.
     *
     * @param  Collection  $items
     * @param  Address  $address
     * @param  array  $data
     * @return  mixed
     */
    public function placeOrder(Collection $items, Address $address, array $data = []);

    public function changeOrderStatus($orderId, $status, $comment, $logger = null, $logLevel = 0);

    public function setOrderShippingMethod($orderId, $shippingMethodId);

    public function updateOrderAddress($orderId, array $data);

    public function getOrderStatistics($orderId);

    public function countOrderProductQtyByLatestMonth($productId, $latestMonth = 1);
}