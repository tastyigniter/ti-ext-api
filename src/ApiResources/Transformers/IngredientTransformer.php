<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Admin\Models\Ingredient;
use League\Fractal\TransformerAbstract;

class IngredientTransformer extends TransformerAbstract
{
    public function transform(Ingredient $ingredient)
    {
        return $ingredient->toArray();
    }
}
