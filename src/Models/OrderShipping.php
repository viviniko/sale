<?php

namespace Viviniko\Sale\Models;

use Viviniko\Currency\Amount;
use Viviniko\Shipping\Models\ShippingMethod;
use Viviniko\Support\Database\Eloquent\Model;

class OrderShipping extends Model
{
    protected $tableConfigKey = 'sale.order_shipping_table';

    public $timestamps = false;

    protected $fillable = [
        'order_id', 'shipping_method_id', 'shipping_country', 'shipping_cost', 'shipping_weight'
    ];

    public function getShippingCostAttribute($shippingCost)
    {
        return Amount::createBaseAmount($shippingCost);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }

}