<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Admin\Models\Menu;
use Igniter\Api\Classes\AbstractRepository;

class MenuRepository extends AbstractRepository
{
    protected $modelClass = Menu::class;

    protected function extendQuery($query)
    {
        $query->with(['menu_options.menu_option_values']);
    }
}
