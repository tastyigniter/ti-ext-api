<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Menus_model;
use Igniter\Api\Classes\AbstractRepository;

class MenuRepository extends AbstractRepository
{
    protected $modelClass = Menus_model::class;

    protected function extendQuery($query)
    {
        $query->with(['menu_options.menu_option_values']);
    }
}
