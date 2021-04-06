<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use System\Models\Currencies_model;

class CurrencyRepository extends AbstractRepository
{
    protected $modelClass = Currencies_model::class;
}
