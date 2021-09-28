<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Menu_options_model;
use League\Fractal\TransformerAbstract;

class MenuOptionTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'option_values',
    ];

    public function transform(Menu_options_model $menuOption)
    {
        return $menuOption->toArray();
    }

    public function includeOptionValues(Menu_options_model $menuOption)
    {
        //When Post/Patch and inside body comes with an json array option_values the deserialized object is a collection of array
        if (is_array($menuOption->option_values)){
            return $this->collection(
                $menuOption->option_values,
                new OptionValueArrayTransformer,
                'option_values'
            );
        }

        return $this->collection(
            $menuOption->option_values,
            new OptionValueTransformer,
            'option_values'
        );
    }
}
