<?php

namespace Viviniko\Sale\Services\Order;

use Viviniko\Sale\Contracts\OrderSNGenerator;
use Viviniko\Sale\Repositories\Order\OrderRepository;

abstract class BaseOrderSNGenerator implements OrderSNGenerator {

    protected $prefix = null;

    protected $prefixLengthLimit = 3;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * The prefix
     *
     * @param string $prefix
     * @return OrderSNGenerator
     */
    public function prefix($prefix)
    {
        $this->prefix = strtoupper($this->prefixLengthLimit ? substr($prefix, 0, $this->prefixLengthLimit) : $prefix);
        return $this;
    }

    protected function unique($sn)
    {
        return $this->orderRepository && $this->orderRepository->exists('order_sn', $sn) ? $this->generate() : $sn;
    }

    protected function random($pool, $length)
    {
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    public function setOrderRepository(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
        return $this;
    }
}