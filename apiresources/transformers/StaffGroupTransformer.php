<?php

namespace Igniter\Api\ApiResources\Transformers;

use League\Fractal\TransformerAbstract;

class UserGroupTransformer extends TransformerAbstract
{
    public function transform(User_groups_model $staffGroup)
    {
        return $staffGroup->toArray();
    }
}
