<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Staff_roles_model;
use Admin\Models\Users_model;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(Users_model $user)
    {
        return $user->toArray();
    }
}
