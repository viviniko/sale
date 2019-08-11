<?php

namespace Viviniko\Sale\Models;

use Illuminate\Support\Facades\Config;
use Viviniko\Currency\Amount;
use Viviniko\Support\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $tableConfigKey = 'sale.order_items_table';

    public $timestamps = false;

    protected $fillable = [
        'order_id', 'product_id', 'item_id', 'price', 'total_discount', 'quantity', 'name', 'sku', 'specs', 'weight'
    ];

    protected $appends = [
        'url'
    ];

    protected $hidden = [
        'item'
    ];

    protected $casts = [
        'specs' => 'array',
    ];

    public function getPriceAttribute($price)
    {
        return Amount::createBaseAmount($price);
    }

    public function getSubtotalAttribute()
    {
        return $this->price->mul($this->quantity);
    }

    public function getTotalDiscountAttribute($totalDiscount)
    {
        return Amount::createBaseAmount($totalDiscount);
    }

    public function order()
    {
        return $this->belongsTo(Config::get('sale.order'), 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Config::get('catalog.product'), 'product_id');
    }

    public function item()
    {
        return $this->belongsTo(Config::get('catalog.item'), 'item_id');
    }

    public function getNameAttribute()
    {
        return data_get($this->product, 'name');
    }

    public function getUrlAttribute()
    {
        return data_get($this->product, 'url');
    }

    public function getImageAttribute()
    {
        return data_get($this->item, 'image');
    }
}