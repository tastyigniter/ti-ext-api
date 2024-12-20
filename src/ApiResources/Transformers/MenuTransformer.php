<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Traits\MergesIdAttribute;
use Igniter\Cart\Models\Menu;
use League\Fractal\TransformerAbstract;

class MenuTransformer extends TransformerAbstract
{
    use MergesIdAttribute;

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
        return $this->mergesIdAttribute($menuItem, [
            'currency' => app('currency')->getDefault()->currency_code,
        ]);
    }

    public function includeMedia(Menu $menuItem)
    {
        return ($thumb = $menuItem->getFirstMedia()) ? $this->item($thumb, new MediaTransformer, 'media') : null;
    }

    public function includeCategories(Menu $menuItem)
    {
        return $this->collection(
            $menuItem->categories,
            new CategoryTransformer,
            'categories',
        );
    }

    public function includeMenuOptions(Menu $menuItem)
    {
        return $this->collection(
            $menuItem->menu_options,
            new MenuItemOptionTransformer,
            'menu_options',
        );
    }

    public function includeIngredients(Menu $menuItem)
    {
        return $this->collection(
            $menuItem->ingredients,
            new IngredientTransformer,
            'ingredients',
        );
    }

    public function includeMealtimes(Menu $menuItem)
    {
        return $this->collection(
            $menuItem->mealtimes,
            new MealtimeTransformer,
            'mealtimes',
        );
    }

    public function includeStocks(Menu $menuItem)
    {
        return $this->collection(
            $menuItem->stocks,
            new StockTransformer,
            'stocks',
        );
    }
}
