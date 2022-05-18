<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Admin\Models\Status;
use League\Fractal\TransformerAbstract;

class StatusTransformer extends TransformerAbstract
{
    public function transform(Status $status)
    {
        return $status->toArray();
    }
}
