<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Customers_model;
use Admin\Models\Locations_model;
use Admin\Models\Menus_model;
use Igniter\Api\Classes\AbstractRepository;

class MenuRepository extends AbstractRepository
{
    protected $modelClass = Menus_model::class;
}