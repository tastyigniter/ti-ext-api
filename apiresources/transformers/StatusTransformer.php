<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Statuses_model;
use League\Fractal\TransformerAbstract;

class StatusTransformer extends TransformerAbstract
{
    public function transform(Statuses_model $status)
    {
        return $status->toArray();
    }
}
