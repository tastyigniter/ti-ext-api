<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Menu_item_options_model;
use League\Fractal\TransformerAbstract;

class MenuItemOptionArrayTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'menu_option_values',
    ];

    public function transform(array $menuItemOption)
    {
        return array_merge($menuItemOption, [
            'id' => $menuItemOption->menu_option_id,
            'option' => $menuItemOption->option,
        ]);
    }

    public function includeMenuOptionValues(Menu_item_options_model $menuItemOption)
    {
        //When Post/Patch and inside body comes with an json array option_values the deserialized object is a collection of array
        if (is_array($menuItemOption->menu_option_values)){
            return $this->collection(
                $menuOption->menu_option_values,
                new MenuItemOptionValueArrayTransformer,
                'menu_option_values'
            );
        }

        return $this->collection(
            $menuItemOption->menu_option_values,
            new MenuItemOptionValueTransformer,
            'menu_option_values'
        );
    }
}
