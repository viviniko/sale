<?php

namespace Viviniko\Sale\Models;

use Illuminate\Support\Facades\Config;
use Viviniko\Support\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $tableConfigKey = 'sale.order_items_table';

    public $timestamps = false;

    protected $fillable = [
        'order_id', 'product_id', 'item_id', 'name', 'sku', 'price', 'quantity', 'description'
    ];

    protected $appends = [
        'picture', 'url'
    ];

    protected $hidden = [
        'item'
    ];

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

    public function getCoverAttribute()
    {
        return data_get($this->item, 'cover');
    }

    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    public function getDescAttrsAttribute()
    {
        return data_get($this->item, 'desc_attrs');
    }
}