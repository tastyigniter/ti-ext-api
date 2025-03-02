<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Locations API Controller
 */
class Locations extends ApiController
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
        'request' => \Igniter\Local\Http\Requests\LocationRequest::class,
        'repository' => Repositories\LocationRepository::class,
        'transformer' => Transformers\LocationTransformer::class,
    ];

    protected string|array $requiredAbilities = ['locations:*'];
}
