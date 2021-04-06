<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Locations_model;
use Igniter\Api\Classes\AbstractRepository;

class LocationRepository extends AbstractRepository
{
    protected $modelClass = Locations_model::class;

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
