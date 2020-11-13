<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Reviews_model;
use Igniter\Api\Classes\AbstractRepository;

class ReviewRepository extends AbstractRepository
{
    protected $modelClass = Reviews_model::class;
}
