<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Menu_item_option_values_model;
use League\Fractal\TransformerAbstract;

class MenuOptionValueTransformer extends TransformerAbstract
{
    public function transform(Menu_item_option_values_model $menuItemOptionValue)
    {
        $valueAsArray = $menuItemOptionValue->toArray();
        data_set($valueAsArray, 'new_price', currency_json($menuItemOptionValue->new_price));
        data_set($valueAsArray, 'price', currency_json($menuItemOptionValue->price));
        data_set($valueAsArray, 'option_value.price', currency_json($menuItemOptionValue->option_value->price));
        return $valueAsArray;
    }
}
