<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Locations API Controller
 */
class Locations extends ApiController
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
        'request' => \Admin\Requests\Location::class,
        'repository' => Repositories\LocationRepository::class,
        'transformer' => Transformers\LocationTransformer::class,
    ];

    protected $requiredAbilities = ['locations:*'];
}
