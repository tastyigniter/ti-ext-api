<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Local\Models\Reviews_model;

class ReviewRepository extends AbstractRepository
{
    protected $modelClass = Reviews_model::class;

    protected static $locationAwareConfig = [];

    protected static $customerAwareConfig = [];
}
