<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Main\Models\Customer;

class CustomerRepository extends AbstractRepository
{
    protected $modelClass = Customer::class;

    protected static $customerAwareConfig = [];
}
