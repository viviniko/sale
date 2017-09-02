<?php

namespace Viviniko\Sale\Models;

use Viviniko\Support\Database\Eloquent\Model;

class OrderVisitor extends Model
{
    protected $tableConfigKey = 'sale.order_visitors_table';

    public $timestamps = false;

    protected $fillable = [
        'order_id', 'ip', 'location', 'user_agent', 'os', 'browser', 'lang', 'referer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}