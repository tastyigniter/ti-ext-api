<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Admin\Models\Table;
use Igniter\Api\Classes\AbstractRepository;

class TableRepository extends AbstractRepository
{
    protected $guarded = [];

    protected $modelClass = Table::class;

    protected static $locationAwareConfig = [];
}
