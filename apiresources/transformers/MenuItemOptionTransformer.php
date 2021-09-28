<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Menu_item_options_model;
use League\Fractal\TransformerAbstract;

class MenuItemOptionTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'menu_option_values',
    ];

    public function transform(Menu_item_options_model $menuItemOption)
    {
        return $menuItemOption->toArray();
    }

    public function includeMenuOptionValues(Menu_item_options_model $menuItemOption)
    {
        return $this->collection(
            $menuItemOption->menu_option_values,
            new MenuItemOptionValueTransformer,
            'menu_option_values'
        );
    }
}
