<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Admin\Models\LocationArea;
use League\Fractal\TransformerAbstract;

class DeliveryAreaTransformer extends TransformerAbstract
{
    public function transform(LocationArea $deliveryArea)
    {
        return $deliveryArea->toArray();
    }
}
