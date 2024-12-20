<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Traits\MergesIdAttribute;
use Igniter\User\Models\Address;
use League\Fractal\TransformerAbstract;

class AddressTransformer extends TransformerAbstract
{
    use MergesIdAttribute;

    public function transform(Address $address)
    {
        return $this->mergesIdAttribute($address);
    }
}
