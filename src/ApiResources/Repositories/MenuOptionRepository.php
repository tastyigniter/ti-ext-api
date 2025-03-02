<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Cart\Models\MenuOption;

class MenuOptionRepository extends AbstractRepository
{
    protected $modelClass = MenuOption::class;

    protected static $locationAwareConfig = [];
}
