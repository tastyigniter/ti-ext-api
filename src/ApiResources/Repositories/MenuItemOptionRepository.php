<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Admin\Models\MenuItemOption;
use Igniter\Api\Classes\AbstractRepository;

class MenuItemOptionRepository extends AbstractRepository
{
    protected $modelClass = MenuItemOption::class;

    protected $fillable = ['menu_option_values'];
}
