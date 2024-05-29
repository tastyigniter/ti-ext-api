<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Categories API Controller
 */
class Categories extends ApiController
{
    public array $implement = [\Igniter\Api\Http\Actions\RestController::class];

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
        'request' => \Igniter\Cart\Http\Requests\CategoryRequest::class,
        'repository' => Repositories\CategoryRepository::class,
        'transformer' => Transformers\CategoryTransformer::class,
    ];

    protected string|array $requiredAbilities = ['categories:*'];
}
