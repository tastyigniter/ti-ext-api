<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\User\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return $user->toArray();
    }
}
