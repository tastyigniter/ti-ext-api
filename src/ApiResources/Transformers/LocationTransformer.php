<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Admin\Models\Location;
use League\Fractal\TransformerAbstract;

class LocationTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'media',
        'working_hours',
        'delivery_areas',
        'reviews',
    ];

    public function transform(Location $location)
    {
        return $location->toArray();
    }

    public function includeMedia(Location $location)
    {
        if (!$thumb = $location->getFirstMedia())
            return null;

        return $this->item($thumb, new MediaTransformer, 'media');
    }

    public function includeWorkingHours(Location $location)
    {
        return $this->collection(
            $location->working_hours,
            new WorkingHourTransformer,
            'working_hours'
        );
    }

    public function includeDeliveryAreas(Location $location)
    {
        return $this->collection(
            $location->delivery_areas,
            new DeliveryAreaTransformer,
            'delivery_areas'
        );
    }

    public function includeReviews(Location $location)
    {
        return $this->collection(
            $location->reviews,
            new ReviewTransformer,
            'reviews'
        );
    }
}
