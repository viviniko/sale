<?php

namespace Viviniko\Sale\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Viviniko\Currency\Amount;
use Viviniko\Sale\Enums\OrderStatus;
use Viviniko\Sale\Enums\PaymentStatus;
use Viviniko\Support\Database\Eloquent\Model;

class Order extends Model
{
    use SoftDeletes;

    protected $tableConfigKey = 'sale.orders_table';

    protected $fillable = [
        'order_number', 'status', 'payment_status', 'payment_method', 'coupon_code', 'customer_id', 'processed_at',
        'subtotal', 'total_shipping', 'total_discounts', 'grand_total', 'total_paid', 'total_weight', 'tracking_number',
        'email', 'note', 'landing_site', 'referring_site', 'remote_ip', 'cancel_reason', 'cancelled_at'
    ];

    protected $dates = ['deleted_at', 'cancelled_at', 'processed_at'];

    public function items()
    {
        return $this->hasMany(Config::get('sale.order_item'), 'order_id');
    }
    
    public function getSubtotalAttribute($subtotal)
    {
        return Amount::createBaseAmount($subtotal);
    }

    public function getGrandTotalAttribute($grandTotal)
    {
        return Amount::createBaseAmount($grandTotal);
    }

    public function getTotalDiscountsAttribute($totalDiscounts)
    {
        return Amount::createBaseAmount($totalDiscounts);
    }

    public function getTotalShippingAttribute($totalShipping)
    {
        return Amount::createBaseAmount($totalShipping);
    }

    public function getTotalPaidAttribute($totalPaid)
    {
        return Amount::createBaseAmount($totalPaid);
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

    public function customer()
    {
        return $this->belongsTo(Config::get('sale.customer'), 'customer_id');
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

    public function getShippingFirstNameAttribute()
    {
        return $this->customer->first_name;
    }

    public function getShippingLastNameAttribute()
    {
        return $this->customer->last_name;
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