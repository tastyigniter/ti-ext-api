<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Admin\Models\MenuOption;
use Igniter\Api\Classes\AbstractRepository;

class MenuOptionRepository extends AbstractRepository
{
    protected $modelClass = MenuOption::class;

    protected static $locationAwareConfig = [];
}
