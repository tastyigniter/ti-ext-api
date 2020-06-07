<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Locations API Controller
 */
class Locations extends ApiController
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
        'model' => \Admin\Models\Locations_model::class,
        'transformer' => \Igniter\Api\ApiResources\Transformers\LocationTransformer::class,
        'authorization' => ['index:all', 'store:admin', 'show:admin', 'update:admin', 'destroy:admin'],
    ];

    protected $requiredAbilities = ['locations:*'];
}