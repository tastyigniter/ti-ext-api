<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Staffs_model;
use League\Fractal\TransformerAbstract;

class StaffTransformer extends TransformerAbstract
{
    public function transform(Staffs_model $staff)
    {
        return $staff->toArray();
    }
}
