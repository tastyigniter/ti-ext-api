<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Traits\MergesIdAttribute;
use Igniter\Local\Models\Location;
use League\Fractal\TransformerAbstract;

class LocationTransformer extends TransformerAbstract
{
    use MergesIdAttribute;

    protected array $availableIncludes = [
        'media',
        'working_hours',
        'delivery_areas',
        'reviews',
    ];

    public function transform(Location $location)
    {
        return $this->mergesIdAttribute($location);
    }

    public function includeMedia(Location $location)
    {
        return ($thumb = $location->getFirstMedia()) ? $this->item($thumb, new MediaTransformer, 'media') : null;
    }

    public function includeWorkingHours(Location $location)
    {
        return $this->collection(
            $location->working_hours,
            new WorkingHourTransformer,
            'working_hours',
        );
    }

    public function includeDeliveryAreas(Location $location)
    {
        return $this->collection(
            $location->delivery_areas,
            new DeliveryAreaTransformer,
            'delivery_areas',
        );
    }

    public function includeReviews(Location $location)
    {
        return $this->collection(
            $location->reviews,
            new ReviewTransformer,
            'reviews',
        );
    }
}
