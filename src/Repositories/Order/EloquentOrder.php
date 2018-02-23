<?php

namespace Viviniko\Sale\Repositories\Order;

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

    public function getProductQtyByLatestMonth($productId, $latestMonth = 1)
    {
        $orderTable = Config::get('sale.orders_table');
        $orderProductTable = Config::get('sale.order_products_table');

        return $this->createModel()
            ->join($orderProductTable, "{$orderTable}.id", "=", "{$orderProductTable}.order_id")
            ->select(["quantity"])
            ->where("product_id", $productId)
            ->where("created_at", '>', Carbon::now()->subMonth($latestMonth))
            ->sum("quantity");
    }
}