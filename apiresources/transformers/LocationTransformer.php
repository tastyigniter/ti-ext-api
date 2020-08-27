<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Classes\TransformerAbstract;

class LocationTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'working_hours',
        'delivery_areas',
        'reviews',
    ];

    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'working_hours' => $this->whenLoaded('working_hours'),
            'delivery_areas' => $this->whenLoaded('delivery_areas'),
            'reviews' => $this->whenLoaded('reviews'),
        ]);
    }
}
