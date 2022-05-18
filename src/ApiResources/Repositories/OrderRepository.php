<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Admin\Models\Order;
use Igniter\Api\Classes\AbstractRepository;

class OrderRepository extends AbstractRepository
{
    protected $modelClass = Order::class;
}
