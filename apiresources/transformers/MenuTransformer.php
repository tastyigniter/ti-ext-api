<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Menus_model;
use League\Fractal\TransformerAbstract;

class MenuTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'media',
        'categories',
        'menu_options',
    ];

    public function transform(Menus_model $menuItem)
    {
        return array_merge($menuItem->toArray(), [
            'menu_price' => currency_json($menuItem->menu_price),
        ]);
    }

    public function includeMedia(Menus_model $menuItem)
    {
        if (!$thumb = $menuItem->getFirstMedia())
            return null;

        return $this->item($thumb, new MediaTransformer, 'media');
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
