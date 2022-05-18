<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Admin\Models\Staff;
use League\Fractal\TransformerAbstract;

class StaffTransformer extends TransformerAbstract
{
    public function transform(Staff $staff)
    {
        return $staff->toArray();
    }
}
