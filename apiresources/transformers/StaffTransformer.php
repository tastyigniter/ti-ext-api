<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Users_model;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(Users_model $staff)
    {
        return $staff->toArray();
    }
}
