<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Menu_item_option_values_model;
use League\Fractal\TransformerAbstract;

class MenuItemOptionValueTransformer extends TransformerAbstract
{
    public function transform(Menu_item_option_values_model $menuItemOptionValue)
    {
        return array_merge($menuItemOptionValue->toArray(), [
            'currency' => app('currency')->getDefault()->currency_code,
        ]);
    }
}
