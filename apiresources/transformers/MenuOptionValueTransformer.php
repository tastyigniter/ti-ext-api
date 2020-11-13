<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Menu_item_option_values_model;
use League\Fractal\TransformerAbstract;

class MenuOptionValueTransformer extends TransformerAbstract
{
    public function transform(Menu_item_option_values_model $menuItemOptionValue)
    {
        return $menuItemOptionValue->toArray();
    }
}
