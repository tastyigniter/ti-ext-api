<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Cart\Models\Order;

class OrderRepository extends AbstractRepository
{
    protected $modelClass = Order::class;

    protected static $locationAwareConfig = [];

    protected static $customerAwareConfig = [];
}
