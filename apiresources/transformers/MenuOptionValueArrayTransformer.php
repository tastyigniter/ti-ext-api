<?php

namespace Igniter\Api\ApiResources\Transformers;

use League\Fractal\TransformerAbstract;

class MenuOptionValueArrayTransformer extends TransformerAbstract
{
    public function transform(array $menuOptionValue)
    {
        $menuOptionValue['currrency'] = app('currency')->getDefault()->currency_code;

        return $menuOptionValue;
    }
}
