<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Cart\Models\Menu;
use League\Fractal\TransformerAbstract;

class MenuTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'media',
        'categories',
        'menu_options',
        'ingredients',
        'mealtimes',
        'stocks',
    ];

    public function transform(Menu $menuItem)
    {
        return array_merge($menuItem->toArray(), [
            'currency' => app('currency')->getDefault()->currency_code,
        ]);
    }

    public function includeMedia(Menu $menuItem)
    {
        if (!$thumb = $menuItem->getFirstMedia()) {
            return null;
        }

        return $this->item($thumb, new MediaTransformer, 'media');
    }

    public function includeCategories(Menu $menuItem)
    {
        return $this->collection(
            $menuItem->categories,
            new CategoryTransformer,
            'categories'
        );
    }

    public function includeMenuOptions(Menu $menuItem)
    {
        return $this->collection(
            $menuItem->menu_options,
            new MenuItemOptionTransformer,
            'menu_options'
        );
    }

    public function includeIngredients(Menu $menuItem)
    {
        return $this->collection(
            $menuItem->ingredients,
            new IngredientTransformer,
            'ingredients'
        );
    }

    public function includeMealtimes(Menu $menuItem)
    {
        return $this->collection(
            $menuItem->mealtimes,
            new MealtimeTransformer,
            'mealtimes'
        );
    }

    public function includeStocks(Menu $menuItem)
    {
        return $this->collection(
            $menuItem->stocks,
            new StockTransformer,
            'stocks'
        );
    }
}
