<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Cart\Models\MenuItemOptionValue;
use League\Fractal\TransformerAbstract;

class MenuItemOptionValueTransformer extends TransformerAbstract
{
    public function transform(array|MenuItemOptionValue $menuItemOptionValue)
    {
        if (!is_array($menuItemOptionValue)) {
            $menuItemOptionValue = $menuItemOptionValue->toArray();
        }

        return array_merge($menuItemOptionValue, [
            'id' => $menuItemOptionValue['menu_option_value_id'],
            'currency' => app('currency')->getDefault()->currency_code,
        ]);
    }
}
