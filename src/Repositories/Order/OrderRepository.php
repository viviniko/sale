<?php

namespace Viviniko\Sale\Repositories\Order;

use Viviniko\Repository\CrudRepository;

interface OrderRepository extends CrudRepository
{
    /**
     * Get order by order number.
     *
     * @param $orderNumber
     * @return mixed
     */
    public function findByOrderNumber($orderNumber);

    /**
     * @param $productId
     * @param int $latestMonthNum
     * @return int
     */
    public function countProductQtyByLatestMonth($productId, $latestMonthNum);
}