<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Categories API Controller
 */
class Categories extends ApiController
{
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
        'model' => \Admin\Models\Categories_model::class,
        'transformer' => \Igniter\Api\ApiResources\Transformers\CategoryTransformer::class,
        'authorization' => ['index:all', 'store:admin', 'show:all', 'update:admin', 'destroy:admin'],
    ];

    protected $requiredAbilities = ['categories:*'];
}