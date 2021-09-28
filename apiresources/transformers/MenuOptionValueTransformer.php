<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Menu_option_values_model;
use League\Fractal\TransformerAbstract;

class MenuOptionValueTransformer extends TransformerAbstract
{
    public function transform(Menu_option_values_model $menuOptionValue)
    {
        return array_merge($menuOptionValue->toArray(), [
            'currency' => app('currency')->getDefault()->currency_code,
        ]);
    }
}
