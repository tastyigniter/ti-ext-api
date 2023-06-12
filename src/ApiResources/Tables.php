<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Tables API Controller
 */
class Tables extends ApiController
{
    public $implement = [\Igniter\Api\Http\Actions\RestController::class];

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
        'request' => \Igniter\Reservation\Requests\TableRequest::class,
        'repository' => Repositories\TableRepository::class,
        'transformer' => Transformers\TableTransformer::class,
    ];

    protected $requiredAbilities = ['tables:*'];
}
