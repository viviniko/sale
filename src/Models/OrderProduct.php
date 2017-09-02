<?php

namespace Viviniko\Sale\Models;

use Viviniko\Catalog\Models\Product;
use Viviniko\Support\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $tableConfigKey = 'sale.order_products_table';

    public $timestamps = false;

    protected $fillable = [
        'order_id', 'product_id', 'name', 'sku', 'price', 'market_price', 'quantity', 'attrs', 'description'
    ];

    public $casts = [
        'attrs' => 'array'
    ];

    protected $appends = [
        'picture', 'url'
    ];

    protected $hidden = [
        'product'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getNameAttribute()
    {
        return data_get($this->product, 'name') ?? $this->attributes['name'];
    }

    public function getUrlAttribute()
    {
        return data_get($this->product, 'url');
    }

    public function getPictureAttribute()
    {
        return app(\Common\Catalog\Contracts\ProductService::class)->getProductPicture($this->product_id, $this->attrs);
    }

    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    public function getGrossWeightAttribute()
    {
        return $this->weight * $this->quantity;
    }

    public function getAttributeValuesAttribute()
    {
        return app(\Common\Catalog\Contracts\AttributeService::class)->findIn($this->attrs)->sort(function ($a, $b) {
            foreach ($this->attrs as $attr) {
                if ($a->id == $attr) {
                    return -1;
                }
                if ($b->id == $attr) {
                    return 1;
                }
            }
            return 0;
        })->pluck('value', 'group.text_prompt');
    }
}