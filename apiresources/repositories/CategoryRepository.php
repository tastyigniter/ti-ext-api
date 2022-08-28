<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Categories_model;
use Igniter\Api\Classes\AbstractRepository;

class CategoryRepository extends AbstractRepository
{
    protected $modelClass = Categories_model::class;

    protected static $locationAwareConfig = [];
}
