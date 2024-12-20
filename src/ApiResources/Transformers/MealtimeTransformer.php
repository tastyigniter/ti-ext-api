<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Traits\MergesIdAttribute;
use Igniter\Cart\Models\Mealtime;
use League\Fractal\TransformerAbstract;

class MealtimeTransformer extends TransformerAbstract
{
    use MergesIdAttribute;

    public function transform(Mealtime $mealTime)
    {
        return $this->mergesIdAttribute($mealTime);
    }
}
