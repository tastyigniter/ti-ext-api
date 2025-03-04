<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Traits\MergesIdAttribute;
use Igniter\User\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    use MergesIdAttribute;

    public function transform(User $user): array
    {
        return $this->mergesIdAttribute($user);
    }
}
