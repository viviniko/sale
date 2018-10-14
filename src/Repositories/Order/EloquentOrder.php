<?php

namespace Viviniko\Sale\Repositories\Order;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Viviniko\Repository\EloquentRepository;

class EloquentOrder extends EloquentRepository implements OrderRepository
{
    public function __construct()
    {
        parent::__construct(Config::get('sale.order'));
    }

    /**
     * {@inheritdoc}
     */
    public function countProductQtyByLatestMonth($productId, $latestMonthNum)
    {
        $orderTable = Config::get('sale.orders_table');
        $orderItemTable = Config::get('sale.order_items_table');

        return $this->createModel()
            ->join($orderItemTable, "{$orderTable}.id", "=", "{$orderItemTable}.order_id")
            ->select(["quantity"])
            ->where("product_id", $productId)
            ->where("created_at", '>', Carbon::now()->subMonth($latestMonthNum))
            ->sum("quantity");
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id, $force = false)
    {
        if ($force === true) {
            // todo
        }

        return parent::delete($id);
    }
}