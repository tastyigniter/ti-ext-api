<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Cart\Models\MenuItemOption;
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
        return $this->collection(
            $menuItemOption->menu_option_values,
            new MenuItemOptionValueTransformer,
            'menu_option_values',
        );
    }
}
