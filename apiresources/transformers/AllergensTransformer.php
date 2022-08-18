<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Allergens_model;
use League\Fractal\TransformerAbstract;

class AllergensTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'allergens_values',
    ];

    public function transform(Allergens_model $allergen)
    {
        return $allergen->toArray();
    }

}
