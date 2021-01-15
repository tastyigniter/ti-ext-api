<?php

namespace Igniter\Api\ApiResources\Repositories;

use System\Models\Currencies_model;
use Igniter\Api\Classes\AbstractRepository;

class CurrencyRepository extends AbstractRepository
{
    protected $modelClass = Currencies_model::class;
}
