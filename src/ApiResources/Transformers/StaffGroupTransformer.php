<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Admin\Models\UserGroup;
use League\Fractal\TransformerAbstract;

class StaffGroupTransformer extends TransformerAbstract
{
    public function transform(UserGroup $staffGroup)
    {
        return $staffGroup->toArray();
    }
}
