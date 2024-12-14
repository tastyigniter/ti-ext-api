<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\User\Models\UserGroup;
use League\Fractal\TransformerAbstract;

class UserGroupTransformer extends TransformerAbstract
{
    public function transform(UserGroup $userGroup)
    {
        return $userGroup->toArray();
    }
}
