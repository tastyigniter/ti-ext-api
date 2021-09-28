<?php

namespace Igniter\Api\ApiResources\Transformers;

use League\Fractal\TransformerAbstract;

class OptionValueArrayTransformer extends TransformerAbstract
{
    public function transform(array $menuItemOptionValue)
    {
        $menuItemOptionValue['currrency'] = app('currency')->getDefault()->currency_code;

        return $menuItemOptionValue;
    }
}
