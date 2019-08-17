<?php

namespace Viviniko\Sale\Models;

use Viviniko\Support\Database\Eloquent\Model;

class OrderAddress extends Model
{
    protected $tableConfigKey = 'sale.order_addresses_table';

    public $timestamps = false;

    protected $fillable = [
        'order_id', 'name',
        'street1', 'street2', 'city_name',
        'state', 'state_name',
        'country', 'country_name',
        'phone', 'postal_code',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function getReadableAttribute()
    {
        $splices = collect([]);
        $splices->push($this->street1);
        $splices->push($this->street2);
        $splices->push($this->city_name);
        $splices->push($this->state_name);
        $splices->push($this->country_name);
        $splices->push($this->postal_code);

        return $splices->filter(function ($item) {
            return !empty($item);
        })->implode(', ');
    }
}