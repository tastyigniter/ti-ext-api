<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Admin\Models\Category;
use Igniter\Api\Classes\AbstractRepository;

class CategoryRepository extends AbstractRepository
{
    protected $modelClass = Category::class;
}
