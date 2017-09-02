<?php

namespace Viviniko\Sale\Listeners;

use Carbon\Carbon;
use Viviniko\Payment\Contracts\PayPalService;
use Viviniko\Sale\Contracts\OrderService;
use Viviniko\Sale\Enums\OrderStatus;

class PayPalEventSubscriber
{
    protected $handlers = [
        'PAYPAL.PAYMENT.SALE.COMPLETED' => 'onPaymentSaleCompleted',
        'PAYPAL.PAYMENT.SALE.DECLINED' => 'onPaymentSaleDeclined',
        'PAYPAL.PAYMENT.SALE.PENDING' => 'onPaymentSalePending',
        'PAYPAL.PAYMENT.SALE.REFUNDED' => 'onPaymentSaleRefunded',
        'PAYPAL.PAYMENT.SALE.REVERSED' => 'onPaymentSaleReversed',
        'PAYPAL.PAYMENT.SALE.CANCELED_REVERSAL' => 'onPaymentSaleCanceledReversal',
    ];

    /**
     * @var PayPalService
     */
    protected $payPalService;

    /**
     * @var OrderService
     */
    protected $orderService;

    public function __construct(OrderService $orderService, PayPalService $payPalService)
    {
        $this->payPalService = $payPalService;
        $this->orderService = $orderService;
    }

    public function onPaymentSaleCompleted($messages)
    {
        $order = $this->getOrderFromMessages($messages);
        $data = $this->parsePPEventMessages($messages);
        $data['order_id'] = data_get($order, 'id');
        $data['summary'] = 'Payment completed for ' . $data['amount'];
        $data['event_type'] = 'PAYMENT.SALE.COMPLETED';
        $this->payPalService->createPayPalEvent($data);
        if ($order) {
            $this->orderService->changeOrderStatus($order->id, [
                'status' => OrderStatus::PAID,
                'payment_status' => $data['status']
            ], $data['summary'], 'System');
        }
    }

    public function onPaymentSalePending($messages)
    {
        $order = $this->getOrderFromMessages($messages);
        $data = $this->parsePPEventMessages($messages);
        $data['order_id'] = data_get($order, 'id');
        $data['summary'] = 'Payment pending for ' . $data['amount'];
        $data['event_type'] = 'PAYMENT.SALE.PENDING';
        $this->payPalService->createPayPalEvent($data);
        if ($order) {
            $this->orderService->changeOrderStatus($order->id, [
                'status' => OrderStatus::PENDING,
                'payment_status' => $data['status']
            ], $data['summary'], 'System');
        }
    }

    public function onPaymentSaleRefunded($messages)
    {
        $order = $this->getOrderFromMessages($messages);
        $data = $this->parsePPEventMessages($messages);
        $data['order_id'] = data_get($order, 'id');
        $data['summary'] = 'A ' . $data['amount'] . ' sale payment was refunded';
        $data['event_type'] = 'PAYMENT.SALE.REFUNDED';
        $this->payPalService->createPayPalEvent($data);
        if ($order) {
            $this->orderService->changeOrderStatus($order->id, [
                'status' => OrderStatus::REFUNDED,
                'payment_status' => $data['status']
            ], $data['summary'], 'System');
        }
    }

    public function onPaymentSaleReversed($messages)
    {
        $order = $this->getOrderFromMessages($messages);
        $data = $this->parsePPEventMessages($messages);
        $data['order_id'] = data_get($order, 'id');
        $data['summary'] = 'A ' . $data['amount'] . ' sale payment was reversed';
        $data['event_type'] = 'PAYMENT.SALE.REVERSED';
        $this->payPalService->createPayPalEvent($data);
        if ($order) {
            $this->orderService->changeOrderStatus($order->id, [
                'status' => OrderStatus::DECIEVE,
                'payment_status' => $data['status']
            ], $data['summary'], 'System');
        }
    }

    public function onPaymentSaleDeclined($messages)
    {
        $order = $this->getOrderFromMessages($messages);
        $data = $this->parsePPEventMessages($messages);
        $data['order_id'] = data_get($order, 'id');
        $data['summary'] = 'A ' . $data['amount'] . ' sale payment was denied';
        $data['event_type'] = 'PAYMENT.SALE.DECLINED';
        $this->payPalService->createPayPalEvent($data);
        if ($order) {
            $this->orderService->changeOrderStatus($order->id, [
                'status' => OrderStatus::DENIED,
                'payment_status' => $data['status']
            ], $data['summary'], 'System');
        }
    }

    public function onPaymentSaleCanceledReversal($messages)
    {
        $order = $this->getOrderFromMessages($messages);
        $data = $this->parsePPEventMessages($messages);
        $data['order_id'] = data_get($order, 'id');
        $data['summary'] = 'A ' . $data['amount'] . ' sale payment was canceled reversal';
        $data['event_type'] = 'PAYMENT.SALE.CANCELED_REVERSAL';
        $this->payPalService->createPayPalEvent($data);
        if ($order) {
            $this->orderService->changeOrderStatus($order->id, [
                'status' => OrderStatus::CANCELED_REVERSAL,
                'payment_status' => $data['status']
            ], $data['summary'], 'System');
        }
    }

    protected function getOrderFromMessages($messages)
    {
        $orderSN = data_get($messages, 'invoice');
        if ($orderSN) {
            return $this->orderService->findByOrderSN($orderSN);
        }

        return null;
    }

    protected function parsePPEventMessages($messages)
    {
        return [
            'event_id' => data_get($messages, 'verify_sign'),
            'resource_type' => 'IPN',
            'event_type' => strtolower(data_get($messages, 'payment_status')),
            'status' => data_get($messages, 'payment_status'),
            'amount' => data_get($messages, 'mc_gross', 'Unknown') . ' ' . data_get($messages, 'mc_currency', 'Unknown'),
            'create_time' => new Carbon(),
            'payment_mode' => data_get($messages, 'payment_type'),
            'summary' => null,
            'reason_code' => data_get($messages, 'reason_code'),
            'resource' => collect($messages)->map(function($item) {
                if (is_string($item)) {
                    try {
                        return mb_convert_encoding($item, 'UTF-8', 'auto');
                    } catch (\Exception $e) {
                        Log::error('Convert encoding error: ' . $item);
                    }

                    return 'base64,' . base64_encode($item);
                }
                return $item;
            })->toJson(),
            'created_at' => new Carbon(),
        ];
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        foreach ($this->handlers as $event => $handler) {
            $events->listen($event, 'Viviniko\Sale\Listeners\PayPalEventSubscriber@' . $handler);
        }
    }
}