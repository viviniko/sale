<?php

return [
    'order' => 'Viviniko\Sale\Models\Order',

    'order_product' => 'Viviniko\Sale\Models\OrderProduct',

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
    | This is the sale_order_products table.
    |
    */
    'order_products_table' => 'sale_order_products',

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