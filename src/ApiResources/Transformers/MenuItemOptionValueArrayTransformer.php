<?php

namespace Igniter\Api\ApiResources\Transformers;

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
