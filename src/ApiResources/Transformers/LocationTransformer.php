<?php

declare(strict_types=1);

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

    public function transform(Location $location): array
    {
        return $this->mergesIdAttribute($location);
    }

    public function includeMedia(Location $location): ?\League\Fractal\Resource\Item
    {
        return ($thumb = $location->getFirstMedia()) ? $this->item($thumb, new MediaTransformer, 'media') : null;
    }

    public function includeWorkingHours(Location $location): ?\League\Fractal\Resource\Collection
    {
        return $this->collection(
            $location->working_hours,
            new WorkingHourTransformer,
            'working_hours',
        );
    }

    public function includeDeliveryAreas(Location $location): ?\League\Fractal\Resource\Collection
    {
        return $this->collection(
            $location->delivery_areas,
            new DeliveryAreaTransformer,
            'delivery_areas',
        );
    }

    public function includeReviews(Location $location): ?\League\Fractal\Resource\Collection
    {
        return $this->collection(
            $location->reviews,
            new ReviewTransformer,
            'reviews',
        );
    }
}
