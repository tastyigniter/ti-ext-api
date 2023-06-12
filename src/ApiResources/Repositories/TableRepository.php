<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Reservation\Models\Table;

class TableRepository extends AbstractRepository
{
    protected $guarded = [];

    protected $modelClass = Table::class;

    protected static $locationAwareConfig = [];
}
