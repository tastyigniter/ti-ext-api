<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Menu_option_values_model;
use League\Fractal\TransformerAbstract;

class OptionValueTransformer extends TransformerAbstract
{
    public function transform(Menu_option_values_model $menuItemOptionValue)
    {
        return array_merge($menuItemOptionValue->toArray(), [
            'currency' => app('currency')->getDefault()->currency_code,
        ]);
    }
}
