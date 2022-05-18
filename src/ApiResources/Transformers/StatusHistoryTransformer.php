<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Admin\Models\StatusHistory;
use League\Fractal\TransformerAbstract;

class StatusHistoryTransformer extends TransformerAbstract
{
    public function transform(StatusHistory $statusHistory)
    {
        return $statusHistory->toArray();
    }
}
