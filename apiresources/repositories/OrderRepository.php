<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Orders_model;
use Igniter\Api\Classes\AbstractRepository;

class OrderRepository extends AbstractRepository
{
    protected $modelClass = Orders_model::class;

    public function getOrderTotalAttribute($value)
    {
        return currency_json($value);
    }
}
