<?php

namespace Viviniko\Sale\Models;

use Viviniko\Support\Database\Eloquent\Model;

class OrderAddress extends Model
{
    protected $tableConfigKey = 'sale.order_addresses_table';

    public $timestamps = false;

    protected $fillable = [
        'order_id', 'name', 'street1', 'street2', 'city_name', 'state_name', 'country_code', 'country_name', 'phone', 'postal_code',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function getFirstnameAttribute()
    {
        return explode(' ', $this->name)[0];
    }

    public function getLastnameAttribute()
    {
        return strpos($this->name, ' ') ? explode(' ', $this->name, 2)[1] : '';
    }
}