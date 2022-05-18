<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Admin\Models\Location;
use Igniter\Api\Classes\AbstractRepository;

class LocationRepository extends AbstractRepository
{
    protected $modelClass = Location::class;

    protected $hidden = ['location_thumb'];

    protected $guarded = [];

    public function getOptionsAttribute($value)
    {
        return array_except($value, ['hours']);
    }

    protected function extendQuery($query)
    {
        $query->select('*');
    }
}
