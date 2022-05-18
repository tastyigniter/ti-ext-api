<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Local\Models\Review;

class ReviewRepository extends AbstractRepository
{
    protected $modelClass = Review::class;
}
