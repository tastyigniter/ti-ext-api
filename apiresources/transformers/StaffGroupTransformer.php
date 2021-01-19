<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Staff_groups_model;
use League\Fractal\TransformerAbstract;

class StaffGroupTransformer extends TransformerAbstract
{
    public function transform(Staff_groups_model $staffGroup)
    {
        return $staffGroup->toArray();
    }
}
