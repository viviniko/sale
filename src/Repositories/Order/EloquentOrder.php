<?php

namespace Viviniko\Sale\Repositories\Order;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Viviniko\Repository\SimpleRepository;

class EloquentOrder extends SimpleRepository implements OrderRepository
{
    protected $modelConfigKey = 'sale.order';

    protected $fieldSearchable = [
        'id',
        'order_sn' => 'like',
        'customer_id',
        'status',
        'created_at' => 'betweenDate',
    ];

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
}