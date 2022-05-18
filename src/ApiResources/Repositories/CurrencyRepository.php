<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\System\Models\Currency;

class CurrencyRepository extends AbstractRepository
{
    protected $modelClass = Currency::class;
}
