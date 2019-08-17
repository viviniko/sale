<?php

namespace Viviniko\Sale\Models;

use Viviniko\Currency\Money;
use Viviniko\Support\Database\Eloquent\Model;

class OrderShipping extends Model
{
    protected $tableConfigKey = 'sale.order_shipping_table';

    public $timestamps = false;

    protected $fillable = [
        'order_id', 'shipping_method', 'country', 'price', 'total_discount',
    ];

    public function getPriceAttribute($price)
    {
        return Money::create($price);
    }

    public function getTotalDiscountAttribute($totalDiscount)
    {
        return Money::create($totalDiscount);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

}