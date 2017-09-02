<?php

namespace Viviniko\Sale\Repositories\OrderProduct;

use Viviniko\Repository\SimpleRepository;

class EloquentOrderProduct extends SimpleRepository implements OrderProductRepository
{
    protected $modelConfigKey = 'sale.order_product';
}