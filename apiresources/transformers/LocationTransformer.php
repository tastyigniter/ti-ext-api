<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Locations_model;
use League\Fractal\TransformerAbstract;

class LocationTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'media',
        'working_hours',
        'delivery_areas',
        'reviews',
    ];

    public function transform(Locations_model $location)
    {
        return $location->toArray();
    }

    public function includeMedia(Locations_model $location)
    {
        if (!$thumb = $location->getFirstMedia())
            return null;

        return $this->item($thumb, new MediaTransformer, 'media');
    }

    public function includeWorkingHours(Locations_model $location)
    {
        return $this->collection(
            $location->working_hours,
            new WorkingHourTransformer,
            'working_hours'
        );
    }

    public function includeDeliveryAreas(Locations_model $location)
    {
        return $this->collection(
            $location->delivery_areas,
            new DeliveryAreaTransformer,
            'delivery_areas'
        );
    }

    public function includeReviews(Locations_model $location)
    {
        return $this->collection(
            $location->reviews,
            new ReviewTransformer,
            'reviews'
        );
    }
}
