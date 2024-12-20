<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Traits\MergesIdAttribute;
use Igniter\Local\Models\LocationArea;
use League\Fractal\TransformerAbstract;

class DeliveryAreaTransformer extends TransformerAbstract
{
    use MergesIdAttribute;

    public function transform(LocationArea $deliveryArea)
    {
        return $this->mergesIdAttribute($deliveryArea);
    }
}
