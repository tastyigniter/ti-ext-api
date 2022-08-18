<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Allergens_model;
use League\Fractal\TransformerAbstract;

class AllergensTransformer extends TransformerAbstract
{
    public function transform(Allergens_model $allergen)
    {
        return $allergen->toArray();
    }
}
