<?php

namespace Igniter\Api\ApiResources\Transformers;

use League\Fractal\TransformerAbstract;

class StaffTransformer extends TransformerAbstract
{
    public function transform(Staff $staff)
    {
        return $staff->toArray();
    }
}
