<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Menus_model;
use League\Fractal\TransformerAbstract;

class MenuTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'categories',
        'menu_options',
    ];

    public function transform(Menus_model $menuItem)
    {
        return array_merge(
            $menuItem->toArray(),
            [
                'media' => $menuItem->getMedia(),
            ]
        );
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
}
