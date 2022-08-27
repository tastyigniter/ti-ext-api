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
        'mealtimes',
        'stocks',
    ];

    public function transform(Menus_model $menuItem)
    {
        return array_merge($menuItem->toArray(), [
            'currency' => app('currency')->getDefault()->currency_code,
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
            new MenuItemOptionTransformer,
            'menu_options'
        );
    }

    public function includeAllergens(Menus_model $menuItem)
    {
        return $this->collection(
            $menuItem->allergens,
            new AllergensTransformer,
            'allergens'
        );
    }

    public function includeMealtimes(Menus_model $menuItem)
    {
        return $this->collection(
            $menuItem->mealtimes,
            new MealtimeTransformer,
            'mealtimes'
        );
    }

    public function includeStocks(Menus_model $menuItem)
    {
        return $this->collection(
            $menuItem->stocks,
            new StockTransformer,
            'stocks'
        );
    }
}
