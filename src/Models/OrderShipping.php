<?php

namespace Viviniko\Sale\Models;

use Viviniko\Currency\Amount;
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
        return Amount::createBaseAmount($price);
    }

    public function getTotalDiscountAttribute($totalDiscount)
    {
        return Amount::createBaseAmount($totalDiscount);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

}