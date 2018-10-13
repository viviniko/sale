<?php

namespace Viviniko\Sale\Services;

use Viviniko\Address\Models\Address;
use Viviniko\Cart\Services\Collection;

interface OrderService
{
    /**
     * Paginate the given query into a simple paginator.
     *
     * @param $pageSize
     * @param array $wheres
     * @param array $orders
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($pageSize, $wheres = [], $orders = []);

    /**
     * Get order.
     *
     * @param $id
     * @return mixed
     */
    public function getOrder($id);

    /**
     * Get order.
     *
     * @param $orderNumber
     * @return mixed
     */
    public function getOrderByOrderNumber($orderNumber);

    /**
     * Create order.
     *
     * @param  Collection  $items
     * @param  Address  $address
     * @param  array  $data
     * @return  mixed
     */
    public function placeOrder(Collection $items, Address $address, array $data = []);

    public function updateOrder($orderId, array $data);

    public function deleteOrder($orderId);

    public function changeOrderStatus($orderId, $status, $comment, $logger = null, $logLevel = 0);

    public function setOrderShippingMethod($orderId, $shippingMethodId);

    public function updateOrderAddress($orderId, array $data);

    public function getOrderStatistics($orderId);

    public function countOrderProductQtyByLatestMonth($productId, $latestMonth = 1);
}