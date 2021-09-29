<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Menu_item_option_values_model;
use League\Fractal\TransformerAbstract;

class MenuItemOptionValueArrayTransformer extends TransformerAbstract
{
    public function transform(array $menuItemOptionValue)
    {
        return array_merge($menuItemOptionValue, [
            'currency' => app('currency')->getDefault()->currency_code,
        ]);
    }
}
