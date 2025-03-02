<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Cart\Models\Category;

class CategoryRepository extends AbstractRepository
{
    protected $modelClass = Category::class;

    protected static $locationAwareConfig = [];
}
