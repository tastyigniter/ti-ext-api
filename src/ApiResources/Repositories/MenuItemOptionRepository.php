<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Cart\Models\MenuItemOption;

class MenuItemOptionRepository extends AbstractRepository
{
    protected $modelClass = MenuItemOption::class;

    protected $fillable = ['menu_option_values'];
}
