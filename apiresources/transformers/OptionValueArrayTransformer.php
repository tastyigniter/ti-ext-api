<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Menu_option_values_model;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;

class OptionValueArrayTransformer extends TransformerAbstract
{
    public function transform(array $menuItemOptionValue)
    {
        $menuItemOptionValue['currrency'] = app('currency')->getDefault()->currency_code;
        return $menuItemOptionValue;
    }
}
