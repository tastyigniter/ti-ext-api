<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Addresses_model;
use League\Fractal\TransformerAbstract;

class AddressTransformer extends TransformerAbstract
{
    public function transform(Addresses_model $address)
    {
        return $address->toArray();
    }
}
