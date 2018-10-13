<?php

namespace Viviniko\Sale\Repositories\Order;

use Viviniko\Repository\SearchRequest;

interface OrderRepository
{
    /**
     * Search.
     *
     * @param SearchRequest $searchRequest
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function search(SearchRequest $searchRequest);

    /**
     * Get order.
     *
     * @param $column
     * @param null $value
     * @param array $columns
     * @return mixed
     */
    public function findBy($column, $value = null, $columns = ['*']);

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
     * Delete a entity in repository by id
     *
     * @param        $id
     * @param  bool  $force
     *
     * @return mixed
     */
    public function delete($id, $force = false);

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