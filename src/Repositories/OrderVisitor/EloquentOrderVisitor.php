<?php

namespace Viviniko\Sale\Repositories\OrderVisitor;

use Viviniko\Repository\SimpleRepository;

class EloquentOrderVisitor extends SimpleRepository implements OrderVisitorRepository
{
    protected $modelConfigKey = 'sale.order_visitor';
}