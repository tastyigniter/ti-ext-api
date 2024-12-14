<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Local\Models\Location;

class LocationRepository extends AbstractRepository
{
    protected $modelClass = Location::class;

    protected $hidden = ['location_thumb'];

    protected function extendQuery($query)
    {
        $query->select('*');
    }
}
