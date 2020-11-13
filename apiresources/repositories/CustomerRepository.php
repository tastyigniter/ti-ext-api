<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Customers_model;
use Igniter\Api\Classes\AbstractRepository;

class CustomerRepository extends AbstractRepository
{
    protected $modelClass = Customers_model::class;
}
