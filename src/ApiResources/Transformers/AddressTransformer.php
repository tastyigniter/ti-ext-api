<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\User\Models\Address;
use League\Fractal\TransformerAbstract;

class AddressTransformer extends TransformerAbstract
{
    public function transform(Address $address)
    {
        return $address->toArray();
    }
}
