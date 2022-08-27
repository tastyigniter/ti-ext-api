<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Mealtimes_model;
use League\Fractal\TransformerAbstract;

class MealtimeTransformer extends TransformerAbstract
{
    public function transform(Mealtimes_model $mealTime)
    {
        return array_merge($mealTime->toArray(), []);
    }
}
