<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Traits\MergesIdAttribute;
use Igniter\User\Models\Address;
use League\Fractal\TransformerAbstract;

class AddressTransformer extends TransformerAbstract
{
    use MergesIdAttribute;

    public function transform(Address $address): array
    {
        return $this->mergesIdAttribute($address);
    }
}
