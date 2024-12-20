<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Admin\Models\StatusHistory;
use Igniter\Api\Traits\MergesIdAttribute;
use League\Fractal\TransformerAbstract;

class StatusHistoryTransformer extends TransformerAbstract
{
    use MergesIdAttribute;

    public function transform(StatusHistory $statusHistory)
    {
        return $this->mergesIdAttribute($statusHistory);
    }
}
