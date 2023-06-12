<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Cart\Models\MenuOption;
use League\Fractal\TransformerAbstract;

class MenuOptionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'option_values',
    ];

    public function transform(MenuOption $menuOption)
    {
        return array_merge($menuOption->toArray(), [
            'id' => $menuOption->option_id,
        ]);
    }

    public function includeOptionValues(MenuOption $menuOption)
    {
        //When Post/Patch and inside body comes with an json array option_values the deserialized object is a collection of array
        if (is_array($menuOption->option_values)) {
            return $this->collection(
                $menuOption->option_values,
                new MenuOptionValueArrayTransformer,
                'option_values'
            );
        }

        return $this->collection(
            $menuOption->option_values,
            new MenuOptionValueTransformer,
            'option_values'
        );
    }
}
