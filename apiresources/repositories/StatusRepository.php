<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Statuses_model;
use Igniter\Api\Classes\AbstractRepository;

class StatusRepository extends AbstractRepository
{
    protected $modelClass = Statuses_model::class;

    protected static $locationAwareConfig = [];
}
