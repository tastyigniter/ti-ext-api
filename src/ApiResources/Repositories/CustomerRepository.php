<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Admin\Models\Customer;
use Igniter\Api\Classes\AbstractRepository;

class CustomerRepository extends AbstractRepository
{
    protected $modelClass = Customer::class;
}
