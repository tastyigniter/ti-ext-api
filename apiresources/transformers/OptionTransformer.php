<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Menu_option_values_model;
use Admin\Models\Menu_options_model;
use League\Fractal\TransformerAbstract;

class OptionTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'option_values',
    ];

    public function transform(Menu_options_model $menuItemOption)
    {
        return $menuItemOption->toArray();
    }

    public function includeOptionValues(Menu_options_model $menuItemOption)
    {
        return $this->collection(
            $menuItemOption->option_values,
            new OptionValueTransformer,
            'option_values'
        );
    }
}
