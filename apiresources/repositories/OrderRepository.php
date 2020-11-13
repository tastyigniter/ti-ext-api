<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Customers_model;
use Admin\Models\Locations_model;
use Admin\Models\Menus_model;
use Admin\Models\Orders_model;
use Igniter\Api\Classes\AbstractRepository;

class OrderRepository extends AbstractRepository
{
    protected $modelClass = Orders_model::class;
}