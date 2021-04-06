<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Location_areas_model;
use League\Fractal\TransformerAbstract;

class DeliveryAreaTransformer extends TransformerAbstract
{
    public function transform(Location_areas_model $deliveryArea)
    {
        return $deliveryArea->toArray();
    }
}
