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
     * Find data by id
     *
     * @param       $id
     *
     * @return mixed
     */
    public function find($id);

    /**
     * Update a entity in repository by id
     *
     * @param       $id
     * @param array $data
     *
     * @return mixed
     */
    public function update($id, array $data);

    /**
     * @param mixed $column
     * @param null $value
     * @return boolean
     */
    public function exists($column, $value = null);

    /**
     * @param $productId
     * @param int $latestMonthNum
     * @return int
     */
    public function countProductQtyByLatestMonth($productId, $latestMonthNum);
}