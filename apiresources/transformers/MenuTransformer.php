<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Menus_model;
use League\Fractal\TransformerAbstract;

class MenuTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'categories',
        'menu_options',
        //        'menu_options.menu_option_values',
        //        'menu_option_values',
    ];

    public function transform(Menus_model $menuItem)
    {
        return $menuItem->toArray();
    }

    public function includeCategories(Menus_model $menuItem)
    {
        return $this->collection(
            $menuItem->categories,
            new CategoryTransformer,
            'categories'
        );
    }

    public function includeMenuOptions(Menus_model $menuItem)
    {
        return $this->collection(
            $menuItem->menu_options,
            new MenuOptionTransformer,
            'menu_options'
        );
    }

//    public function includeMenuOptionValues(Menus_model $menuItem)
//    {
//        return $this->collection($menuItem->menu_options->menu_option_values, new MenuOptionValueTransformer);
//    }
}
