<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Traits\MergesIdAttribute;
use Igniter\User\Models\UserGroup;
use League\Fractal\TransformerAbstract;

class UserGroupTransformer extends TransformerAbstract
{
    use MergesIdAttribute;

    public function transform(UserGroup $userGroup)
    {
        return $this->mergesIdAttribute($userGroup);
    }
}
