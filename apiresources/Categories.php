<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Categories API Controller
 */
class Categories extends ApiController
{
    public $implement = ['Igniter.Api.Actions.RestController'];

    public $restConfig = [
        'actions' => [
            'index' => [
                'pageLimit' => 20,
            ],
            'store' => [],
            'show' => [],
            'update' => [],
            'destroy' => [],
        ],
        'request' => \Admin\Requests\Category::class,
        'repository' => Repositories\CategoryRepository::class,
        'transformer' => Transformers\CategoryTransformer::class,
    ];

    protected $requiredAbilities = ['categories:*'];
}
