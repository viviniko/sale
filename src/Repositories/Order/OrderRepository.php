<?php

namespace Viviniko\Sale\Repositories\Order;

interface OrderRepository
{
    /**
     * Paginate the given query into a simple paginator.
     *
     * @param int $perPage
     * @param string $searchName
     * @param null $search
     * @param null $order
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage, $searchName = 'search', $search = null, $order = null);

    /**
     * @param mixed $column
     * @param null $value
     * @return boolean
     */
    public function exists($column, $value = null);
}