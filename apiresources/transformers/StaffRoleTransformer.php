<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Staff_roles_model;
use League\Fractal\TransformerAbstract;

class StaffRoleTransformer extends TransformerAbstract
{
    public function transform(Staff_roles_model $staffRole)
    {

        if (isset($staffRole->permissions)) {
            $staffRole->permissions = null;
        }
        return $staffRole->toArray();
    }
}
