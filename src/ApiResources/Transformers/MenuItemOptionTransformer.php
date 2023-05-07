<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Admin\Models\MenuItemOption;
use League\Fractal\TransformerAbstract;

class MenuItemOptionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'menu_option_values',
    ];

    public function transform(MenuItemOption $menuItemOption)
    {
        return array_merge($menuItemOption->toArray(), [
            'id' => $menuItemOption->menu_option_id,
            'option' => $menuItemOption->option,
        ]);
    }

    public function includeMenuOptionValues(MenuItemOption $menuItemOption)
    {
        //When Post/Patch and inside body comes with an json array menu_option_values the deserialized object is a collection of array
        if (is_array($menuItemOption->menu_option_values)) {
            return $this->collection(
                $menuItemOption->menu_option_values,
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
