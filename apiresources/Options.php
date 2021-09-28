<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Options API Controller
 */
class Options extends ApiController
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
        'request' => Requests\OptionRequest::class,
        'repository' => Repositories\MenuOptionRepository::class,
        'transformer' => Transformers\OptionTransformer::class,
    ];

    protected $requiredAbilities = ['options:*'];

}
