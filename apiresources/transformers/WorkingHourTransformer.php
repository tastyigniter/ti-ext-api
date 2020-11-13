<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Working_hours_model;
use League\Fractal\TransformerAbstract;

class WorkingHourTransformer extends TransformerAbstract
{
    public function transform(Working_hours_model $workingHour)
    {
        return $workingHour->toArray();
    }
}
