<?php

namespace Viviniko\Sale\Listeners;

use Viviniko\Catalog\Contracts\ProductService;
use Viviniko\Mail\Contracts\MailService;
use Viviniko\Sale\Contracts\OrderService;
use Viviniko\Sale\Events\OrderCreated;
use Viviniko\Sale\Events\OrderPaid;
use Illuminate\Support\Facades\Log;

class OrderEventSubscriber
{
    protected $handlers = [
        OrderPaid::class => 'onOrderPaid',
        OrderCreated::class => 'onOrderCreated'
    ];

    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * @var MailService
     */
    protected $mailService;

    public function __construct(OrderService $orderService, ProductService $productService, MailService $mailService)
    {
        $this->orderService = $orderService;
        $this->productService = $productService;
        $this->mailService = $mailService;
    }

    public function onOrderCreated(OrderCreated $event)
    {
        $this->mail($event->order, 'order.new');
    }

    public function onOrderPaid(OrderPaid $event)
    {
        foreach ($event->order->products as $product) {
            $this->productService->changeProductStockQuantity($product->product_id, $product->sku, 0-$product->quantity);
        }

        $this->mail($event->order, 'order.paid');
    }

    protected function mail($order, $template)
    {
        try {
            $this->mailService->send($order->customer_email, $template, array_merge($order->toArray(), ['products' => $order->products->toArray(), 'address' => $order->address->toArray()]));
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        foreach ($this->handlers as $event => $handler) {
            $events->listen($event, 'Viviniko\Sale\Listeners\OrderEventSubscriber@' . $handler);
        }
    }
}