<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Cart\Models\Menu;

class MenuRepository extends AbstractRepository
{
    protected $modelClass = Menu::class;

    protected static $locationAwareConfig = [];

    protected function extendQuery($query)
    {
        $query->with(['menu_options.menu_option_values']);
    }
}
