<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Menu_item_options_model;
use League\Fractal\TransformerAbstract;

class MenuOptionTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
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
            new MenuOptionValueTransformer,
            'menu_option_values'
        );
    }
}
