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

    public function item()
    {
        return $this->belongsTo(Config::get('catalog.item'), 'item_id');
    }

    public function getUrlAttribute()
    {
        return data_get($this->item, 'url');
    }

    public function getPictureAttribute()
    {
        return app(\Common\Catalog\Contracts\ProductService::class)->getProductPicture($this->product_id, $this->attrs);
    }

    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}