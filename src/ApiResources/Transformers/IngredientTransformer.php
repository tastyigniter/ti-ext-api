<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Traits\MergesIdAttribute;
use Igniter\Cart\Models\Ingredient;
use League\Fractal\TransformerAbstract;

class IngredientTransformer extends TransformerAbstract
{
    use MergesIdAttribute;

    public function transform(Ingredient $ingredient)
    {
        return $this->mergesIdAttribute($ingredient);
    }
}
