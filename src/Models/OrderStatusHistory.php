<?php

namespace Viviniko\Sale\Models;

use Viviniko\Support\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $tableConfigKey = 'sale.order_status_histories_table';

    public $timestamps = false;

    protected $fillable = [
        'order_id', 'status', 'comment', 'logger', 'log_level', 'created_at'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}