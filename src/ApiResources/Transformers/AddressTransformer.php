<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Admin\Models\Address;
use League\Fractal\TransformerAbstract;

class AddressTransformer extends TransformerAbstract
{
    public function transform(Address $address)
    {
        return $address->toArray();
    }
}
