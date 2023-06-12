<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Cart\Models\MenuItemOptionValue;
use League\Fractal\TransformerAbstract;

class MenuItemOptionValueTransformer extends TransformerAbstract
{
    public function transform(MenuItemOptionValue $menuItemOptionValue)
    {
        return array_merge($menuItemOptionValue->toArray(), [
            'currency' => app('currency')->getDefault()->currency_code,
        ]);
    }
}
