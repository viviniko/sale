<?php

namespace Viviniko\Sale\Listeners;

use Viviniko\Payment\Services\CreditCardService;
use Viviniko\Payment\Enums\PaymentMethod;
use Viviniko\Sale\Services\OrderService;
use Viviniko\Sale\Enums\OrderStatus;
use Viviniko\Sale\Enums\PaymentStatus;

class CreditCardEventSubscriber
{
    protected $handlers = [
        'CREDITCARD.PAYMENT.EXECUTE_SUCCESS' => 'onPaymentSuccess',
    ];

    /**
     * @var CreditCardService
     */
    protected $creditCardService;

    /**
     * @var OrderService
     */
    protected $orderService;

    public function __construct(OrderService $orderService, CreditCardService $creditCardService)
    {
        $this->creditCardService = $creditCardService;
        $this->orderService = $orderService;
    }

    public function onPaymentSuccess($messages)
    {
        $order = $this->getOrderFromMessages($messages);
        if (!$order) {
            return ;
        }

        $status = $messages['status'];
        $orderStatus = OrderStatus::UNPAID;
        $summary = 'Unknown status: ' . $status;
        if ($status == 'success') {
            $status = PaymentStatus::COMPLETED;
            $orderStatus = OrderStatus::PAID;
            $summary = "Credit Card Payment Success.";
        } else if ($status == 'processing') {
            $status = PaymentStatus::IN_PROGRESS;
            $orderStatus = OrderStatus::PAID_PROCESSING;
            $summary = "Credit Card Payment Processing.";
        } else if ($status == 'authorizing') {
            $status = PaymentStatus::AUTHORIZING;
            $orderStatus = OrderStatus::PENDING;
            $summary = "Credit Card Payment Authorizing.";
            $result = $this->creditCardService->processConfirm($messages['merchOrderNo']);
            if (is_array($result) && $result['status'] == 'success') {
                $status = PaymentStatus::COMPLETED;
                $orderStatus = OrderStatus::PAID;
                $summary = "Credit Card Payment Authorizing Success.";
            }
        }

        $this->orderService->changeOrderStatus($order->id, [
            'status' => $orderStatus,
            'payment_status' => $status,
            'payment_method' => PaymentMethod::CREDIT_CARD,
        ], "{$summary} MerchOrderNo: {$messages['merchOrderNo']}", 'System');
    }

    protected function getOrderFromMessages($messages)
    {
        if (!empty($messages['merchOrderNo'])) {
            $orderSN = substr($messages['merchOrderNo'], 1, stripos($messages['merchOrderNo'], 'R') - 1);
            if ($orderSN) {
                return $this->orderService->findByOrderSN($orderSN);
            }
        }

        return null;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        foreach ($this->handlers as $event => $handler) {
            $events->listen($event, 'Viviniko\Sale\Listeners\CreditCardEventSubscriber@' . $handler);
        }
    }
}