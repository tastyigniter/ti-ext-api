<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Reservation\Models\DiningTable;

class DiningTableRepository extends AbstractRepository
{
    protected $modelClass = DiningTable::class;

    protected static $locationAwareConfig = [];
}
