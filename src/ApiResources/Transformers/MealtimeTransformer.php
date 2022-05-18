<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Admin\Models\Mealtime;
use League\Fractal\TransformerAbstract;

class MealtimeTransformer extends TransformerAbstract
{
    public function transform(Mealtime $mealTime)
    {
        return array_merge($mealTime->toArray(), []);
    }
}
