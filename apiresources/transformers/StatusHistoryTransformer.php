<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Status_history_model;
use League\Fractal\TransformerAbstract;

class StatusHistoryTransformer extends TransformerAbstract
{
    public function transform(Status_history_model $statusHistory)
    {
        return $statusHistory->toArray();
    }
}
