<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Locations_model;
use Igniter\Api\Classes\AbstractRepository;

class LocationRepository extends AbstractRepository
{
    protected $modelClass = Locations_model::class;

    protected $hidden = [];
}
