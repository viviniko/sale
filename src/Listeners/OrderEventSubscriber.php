<?php

namespace Viviniko\Sale\Listeners;

use Illuminate\Support\Facades\DB;
use Viviniko\Catalog\Contracts\ItemService;
use Viviniko\Mail\Contracts\MailService;
use Viviniko\Sale\Contracts\OrderService;
use Viviniko\Sale\Events\OrderCreated;
use Viviniko\Sale\Events\OrderPaid;
use Illuminate\Support\Facades\Log;
use Viviniko\Sale\Events\OrderShipped;

class OrderEventSubscriber
{
    protected $handlers = [
        OrderPaid::class => 'onOrderPaid',
        OrderCreated::class => 'onOrderCreated',
        OrderShipped::class => 'onOrderShipped',
    ];

    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * @var ItemService
     */
    protected $itemService;

    /**
     * @var MailService
     */
    protected $mailService;

    public function __construct(OrderService $orderService, ItemService $itemService, MailService $mailService)
    {
        $this->orderService = $orderService;
        $this->itemService = $itemService;
        $this->mailService = $mailService;
    }

    public function onOrderCreated(OrderCreated $event)
    {
        $this->mail($event->order, 'order.new');
    }

    public function onOrderPaid(OrderPaid $event)
    {
        foreach ($event->order->items as $item) {
            $this->itemService->update($item->item_id, ['quantity' => DB::raw('quantity - ' . $item->quantity)]);
        }

        $this->mail($event->order, 'order.paid');
    }

    public function onOrderShipped(OrderShipped $event)
    {
        $this->mail($event->order, 'order.in.transit');
    }

    protected function mail($order, $template)
    {
        try {
            $this->mailService->send($order->customer_email, $template, array_merge($order->toArray(), ['items' => $order->items->toArray(), 'address' => $order->address->toArray()]));
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