<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Cart\Models\MenuOptionValue;
use League\Fractal\TransformerAbstract;

class MenuOptionValueTransformer extends TransformerAbstract
{
    public function transform(array|MenuOptionValue $menuOptionValue)
    {
        if (!is_array($menuOptionValue)) {
            $menuOptionValue = $menuOptionValue->toArray();
        }

        return array_merge($menuOptionValue, [
            'id' => $menuOptionValue['option_value_id'],
            'currency' => app('currency')->getDefault()->currency_code,
        ]);
    }
}
