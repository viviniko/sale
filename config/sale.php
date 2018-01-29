<?php

return [
    'order' => 'Viviniko\Sale\Models\Order',

    'order_item' => 'Viviniko\Sale\Models\OrderItem',

    'order_visitor' => 'Viviniko\Sale\Models\OrderVisitor',

    'order_status_history' => 'Viviniko\Sale\Models\OrderStatusHistory',

    'order_address' => 'Viviniko\Sale\Models\OrderAddress',

    'order_shipping' => 'Viviniko\Sale\Models\OrderShipping',

    /*
    |--------------------------------------------------------------------------
    | Sale Order Table
    |--------------------------------------------------------------------------
    |
    | This is the sale_orders table.
    |
    */
    'orders_table' => 'sale_orders',

    /*
    |--------------------------------------------------------------------------
    | Sale Order Products Table
    |--------------------------------------------------------------------------
    |
    | This is the sale_order_items table.
    |
    */
    'order_items_table' => 'sale_order_items',

    /*
    |--------------------------------------------------------------------------
    | Sale Order address Table
    |--------------------------------------------------------------------------
    |
    | This is the sale_order_address table.
    |
    */
    'order_addresses_table' => 'sale_order_addresses',

    /*
    |--------------------------------------------------------------------------
    | Sale Order Visitors Table
    |--------------------------------------------------------------------------
    |
    | This is the sale_order_visitors table.
    |
    */
    'order_visitors_table' => 'sale_order_visitors',

    'order_shipping_table' => 'sale_order_shipping',

    'order_status_histories_table' => 'sale_order_status_histories',
];