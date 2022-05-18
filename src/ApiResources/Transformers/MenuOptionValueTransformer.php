<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Admin\Models\MenuOptionValue;
use League\Fractal\TransformerAbstract;

class MenuOptionValueTransformer extends TransformerAbstract
{
    public function transform(MenuOptionValue $menuOptionValue)
    {
        return array_merge($menuOptionValue->toArray(), [
            'currency' => app('currency')->getDefault()->currency_code,
        ]);
    }
}
