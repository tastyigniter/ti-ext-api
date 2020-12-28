<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Tables API Controller
 */
class Tables extends ApiController
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
        'request' => \Admin\Requests\Table::class,
        'repository' => Repositories\TableRepository::class,
        'transformer' => Transformers\TableTransformer::class,
    ];

    protected $requiredAbilities = ['tables:*'];
}
