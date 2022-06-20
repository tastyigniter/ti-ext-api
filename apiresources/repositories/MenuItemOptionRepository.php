<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Menu_item_options_model;
use Igniter\Api\Classes\AbstractRepository;

/**
 * @deprecated remove before v4. Added for backward compatibility
 */
class MenuItemOptionRepository extends AbstractRepository
{
    protected $modelClass = Menu_item_options_model::class;

    protected $fillable = ['menu_option_values'];
}
