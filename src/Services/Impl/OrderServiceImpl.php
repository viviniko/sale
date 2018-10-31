<?php

namespace Viviniko\Sale\Services\Impl;

use Carbon\Carbon;
use Viviniko\Address\Models\Address;
use Viviniko\Agent\Facades\Agent;
use Viviniko\Cart\Collection;
use Viviniko\Customer\Services\CustomerService;
use Viviniko\Repository\SearchPageRequest;
use Viviniko\Sale\Services\OrderService;
use Viviniko\Sale\Services\OrderSNGenerator;
use Viviniko\Sale\Enums\OrderStatus;
use Viviniko\Sale\Events\OrderCreated;
use Viviniko\Sale\Events\OrderPaid;
use Viviniko\Sale\Events\OrderShipped;
use Viviniko\Sale\Repositories\Order\OrderRepository;
use Viviniko\Sale\Repositories\OrderAddress\OrderAddressRepository;
use Viviniko\Sale\Repositories\OrderItem\OrderItemRepository;
use Viviniko\Sale\Repositories\OrderShipping\OrderShippingRepository;
use Viviniko\Sale\Repositories\OrderStatusHistory\OrderStatusHistoryRepository;
use Viviniko\Sale\Repositories\OrderVisitor\OrderVisitorRepository;
use Viviniko\Shipping\Services\ShippingService;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class OrderServiceImpl implements OrderService
{
    protected $orders;

    protected $orderItems;

    protected $orderVisitors;

    protected $orderAddresses;

    protected $orderShippings;

    protected $orderStatusHistories;

    protected $customerService;

    protected $shippingService;

    protected $orderSNGenerator;

    /**
     * Instance of the event dispatcher.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    public function __construct(
        OrderRepository $orders,
        OrderItemRepository $orderItems,
        OrderVisitorRepository $orderVisitors,
        OrderAddressRepository $orderAddresses,
        OrderShippingRepository $orderShippings,
        OrderStatusHistoryRepository $orderStatusHistories,
        CustomerService $customerService,
        ShippingService $shippingService,
        OrderSNGenerator $orderSNGenerator,
        Dispatcher $events)
    {
        $this->orders = $orders;
        $this->orderItems = $orderItems;
        $this->orderVisitors = $orderVisitors;
        $this->orderAddresses = $orderAddresses;
        $this->orderShippings = $orderShippings;
        $this->orderStatusHistories = $orderStatusHistories;
        $this->customerService = $customerService;
        $this->shippingService = $shippingService;
        $this->orderSNGenerator = $orderSNGenerator;
        $this->events = $events;
    }

    public function paginate($pageSize, $wheres = [], $orders = [])
    {
        return $this->orders->search(
            SearchPageRequest::create($pageSize, $wheres, $orders)
                ->rules([
                    'id',
                    'order_number' => 'like',
                    'customer_id',
                    'status',
                    'created_at' => 'betweenDate',
                ])
                ->request(request(), 'search')
        );
    }

    public function getOrder($id)
    {
        return $this->orders->find($id);
    }

    public function getOrderByOrderNumber($orderNumber)
    {
        return $this->orders->findBy('order_number', $orderNumber);
    }

    public function placeOrder(Collection $items, Address $address, array $data = [])
    {
        $order = null;
        if (Auth::check()) {
            $customer = Auth::user();
        } else if (isset($data['customer_email'])) {
            $customer = $this->customerService->findByEmail($data['customer_email']);
        }

        if (isset($customer)) {
            $data['customer_first_name'] = $data['customer_first_name'] ?? $customer->first_name;
            $data['customer_last_name'] = $data['customer_last_name'] ?? $customer->last_name;
            $data['customer_id'] = $data['customer_id'] ?? $customer->id;
        }

        DB::transaction(function () use (&$order, $items, $address, $data) {
            $shippingData = [
                'shipping_method_id' => 0,
                'shipping_country' => $address->country,
                'shipping_weight' => $items->total_weight,
                'shipping_cost' => 0,
            ];
            if (isset($data['shipping_method_id'])) {
                $shippingAmount = $this->shippingService->getShippingAmount($data['shipping_method_id'], $shippingData['shipping_country'], $items->total_weight);
                $shippingData['shipping_method_id'] = $data['shipping_method_id'];
                $shippingData['shipping_cost'] = $shippingAmount->value;
                $items->setShippingAmount($shippingAmount);
            }

            $order = $this->orders->create(array_merge([
                'order_number' => $this->orderSNGenerator->generate(),
                'status' => OrderStatus::UNPAID,
                'payment_status' => null,
                'payment_method' => null,
                'coupon_code' => $items->getDiscountCoupon(),
                'customer_id' => Auth::id(),
                'subtotal' => $items->getSubtotal()->value,
                'shipping_amount' => $items->getShippingAmount()->value,
                'discount_amount' => $items->getDiscountAmount()->value,
                'grand_total' => $items->getGrandTotal()->value,
                'customer_note' => null,
                'referer' => null,
                'remote_ip' => Request::ip(),
            ], $data));


            foreach ($items as $item) {
                $this->orderItems->create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'item_id' => $item->item_id,
                    'sku' => $item->sku,
                    'name' => $item->name,
                    'subtotal' => $item->subtotal->value,
                    'amount' => $item->amount->value,
                    'discount' => $item->discount,
                    'quantity' => $item->quantity,
                    'description' => (array) $item->desc_specs,
                ]);
            }

            $this->orderAddresses->create([
                'order_id' => $order->id,
                'name' => $address->firstname . ' ' . $address->lastname,
                'street1' => (string) $address->street1,
                'street2' => (string) $address->street2,
                'city_name' => (string) $address->city_name,
                'state' => (string) $address->state,
                'state_name' => (string) $address->state_name,
                'country' => (string) $address->country,
                'country_name' => (string) $address->country_name,
                'phone' => (string) $address->phone,
                'postal_code' => (string) $address->postal_code,
            ]);

            $shippingData['order_id'] = $order->id;
            $this->orderShippings->create($shippingData);

            $location = Agent::location();
            $platform = Agent::platform();
            $browser = Agent::browser();
            $this->orderVisitors->create([
                'order_id' => $order->id,
                'ip' => Request::ip(),
                'location' => "{$location->country} {$location->state_name} {$location->city}",
                'user_agent' => Agent::getUserAgent(),
                'os' => $platform . Agent::version($platform),
                'browser' => $browser . Agent::version($browser),
                'lang' => implode(',', Agent::languages()),
                'referer' => (string) Agent::referer(),
            ]);
        });

        $this->events->dispatch(new OrderCreated($order));

        return $order;
    }

    public function updateOrder($orderId, array $data)
    {
        return $this->orders->update($orderId, $data);
    }

    public function deleteOrder($orderId)
    {
        return $this->orders->delete($orderId, false);
    }

    public function changeOrderStatus($orderId, $status, $comment, $logger = null, $logLevel = 0)
    {
        return DB::transaction(function () use ($orderId, $status, $comment, $logger, $logLevel) {
            $order = $this->orders->find($orderId);
            $data = $status;
            if (is_array($status)) {
                $status = $status['status'];
            } else {
                $data = ['status' => $status];
            }

            if (!empty($data) && array_key_exists($status, OrderStatus::values())) {
                $this->orders->update($orderId, $data);
            }

            $history = $this->orderStatusHistories->create([
                'order_id' => $orderId,
                'status' => $status,
                'comment' => $comment,
                'logger' => $logger ?? (Auth::user()->first_name . ' ' . Auth::user()->last_name),
                'log_level' => $logLevel,
                'created_at' => new Carbon(),
            ]);

            if ($order) {
                if ($status == OrderStatus::PAID) {
                    event(new OrderPaid($order));
                } else if ($status == OrderStatus::SHIPPED) {
                    event(new OrderShipped($order));
                }
            }

            return $history;
        });
    }

    public function setOrderShippingMethod($orderId, $shippingMethodId)
    {
        $shipping = $this->orderShippings->findBy('order_id', $orderId)->first();
        $oldShippingAmount = $shipping->shipping_cost;
        $shippingCost = $this->shippingService->getShippingAmount($shippingMethodId, $shipping->shipping_country, $shipping->shipping_weight);
        DB::transaction(function () use ($orderId, $shipping, $shippingMethodId, $shippingCost, $oldShippingAmount) {
            $this->orderShippings->update($shipping->id, [
                'shipping_method_id' => $shippingMethodId,
                'shipping_cost' => $shippingCost->value,
            ]);
            if ($order = $this->orders->find($orderId)) {
                $this->orders->update($orderId, ['shipping_amount' => $shippingCost->value, 'grand_total' => $order->grand_total->add($shippingCost)->sub($oldShippingAmount)]);
            }
        });
    }

    public function updateOrderAddress($orderId, array $data)
    {
        $order = $this->orders->find($orderId);
        if ($order) {
            $address = $this->orderAddresses->findBy('order_id', $orderId)->first();
            $shipping = $this->orderShippings->findBy('order_id', $orderId)->first();

            DB::transaction(function () use ($orderId, &$address, $shipping, $order, $data) {
                if ($data['country']) {
                    if ($shipping->shipping_country != $data['country']) {
                        $this->orderShippings->update($shipping->id, ['shipping_country' => $data['country']]);
                        $this->setOrderShippingMethod($orderId, $shipping->shipping_method_id);
                    }
                }

                $address = $this->orderAddresses->update($address->id, $data);
            });

            return $address;
        }
    }

    public function getOrderStatistics($orderId)
    {
        $order = $this->orders->find($orderId);

        return collect(['quantity', 'subtotal', 'grand_total', 'discount_amount', 'shipping_amount'])->mapWithKeys(function ($item) use ($order) {
            return [$item => $order->$item];
        });
    }

    public function countOrderProductQtyByLatestMonth($productId, $latestMonth = 1)
    {
        return $this->orders->countProductQtyByLatestMonth($productId, $latestMonth);
    }
}