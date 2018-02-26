<?php

namespace Viviniko\Sale\Models;

use Illuminate\Support\Facades\Config;
use Viviniko\Payment\Models\PayPalEvent;
use Viviniko\Sale\Enums\OrderStatus;
use Viviniko\Sale\Enums\PaymentStatus;
use Viviniko\Support\Database\Eloquent\Model;

class Order extends Model
{
    protected $tableConfigKey = 'sale.orders_table';

    protected $fillable = [
        'order_sn', 'status', 'payment_status', 'payment_method', 'coupon_code', 'customer_id', 'subtotal', 'shipping_amount', 'discount_amount', 'grand_total',
        'total_paid', 'customer_email', 'customer_firstname', 'customer_lastname', 'customer_note', 'referer', 'remote_ip',
    ];

    public function items()
    {
        return $this->hasMany(Config::get('sale.order_item'), 'order_id');
    }

    public function getProductDetailsAttribute()
    {
        return $this->items->reduce(function ($text, $item) {
            return $text . $item->name . ' * ' . $item->quantity . '<br>';
        }, '');
    }

    public function visitor()
    {
        return $this->hasOne(Config::get('sale.order_visitor'), 'order_id');
    }

    public function address()
    {
        return $this->hasOne(Config::get('sale.order_address'), 'order_id');
    }

    public function shipping()
    {
        return $this->hasOne(OrderShipping::class, 'order_id');
    }

    public function histories()
    {
        return $this->hasMany(Config::get('sale.order_status_history'), 'order_id');
    }

    public function paypalEvent()
    {
        return $this->hasOne(PayPalEvent::class);
    }

    public function getStatusTextAttribute()
    {
        return OrderStatus::values()[$this->attributes['status']] ?? '';
    }

    public function getPaymentStatusTextAttribute()
    {
        return PaymentStatus::values()[$this->attributes['payment_status']] ?? '';
    }

    public function canPay()
    {
        return $this->status == OrderStatus::UNPAID;
    }

    public function getShippingNameAttribute()
    {
        return data_get($this->address, 'name');
    }

    public function getShippingFirstnameAttribute()
    {
        return $this->customer_firstname;
    }

    public function getShippingLastnameAttribute()
    {
        return $this->customer_lastname;
    }

    public function getShippingCountryNameAttribute()
    {
        return data_get($this->address, 'country_name');
    }

    public function getShippingStateNameAttribute()
    {
        return data_get($this->address, 'state_name');
    }

    public function getShippingCityNameAttribute()
    {
        return data_get($this->address, 'city_name');
    }

    public function getShippingStreet1Attribute()
    {
        return data_get($this->address, 'address1');
    }

    public function getShippingStreet2Attribute()
    {
        return data_get($this->address, 'address2');
    }

    public function getShippingPostalCodeAttribute()
    {
        return data_get($this->address, 'postal_code');
    }

    public function getShippingPhoneAttribute()
    {
        return data_get($this->address, 'phone');
    }

    public function getQuantityAttribute()
    {
        return $this->products->sum('quantity');
    }
}