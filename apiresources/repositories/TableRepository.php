<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Tables_model;
use Igniter\Api\Classes\AbstractRepository;

class TableRepository extends AbstractRepository
{
    protected $guarded = [];

    protected $modelClass = Tables_model::class;

    protected static $locationAwareConfig = [];
}
