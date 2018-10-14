<?php

namespace Viviniko\Sale\Repositories\OrderVisitor;

use Viviniko\Repository\EloquentRepository;

class EloquentOrderVisitor extends EloquentRepository implements OrderVisitorRepository
{
    public function __construct()
    {
        parent::__construct(Config::get('sale.order_visitor'));
    }
}